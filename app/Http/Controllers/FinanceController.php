<?php

namespace App\Http\Controllers;

use App\Models\FinanceAccount;
use App\Models\FinanceBudget;
use App\Models\FinanceTransaction;
use App\Models\Spj;
use Illuminate\Support\Facades\Auth;
use App\Models\Village;
use App\Services\Audit\AuditService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $query = FinanceTransaction::with('account');

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($account = $request->input('account_id')) {
            $query->where('account_id', $account);
        }

        $transactions = $query->latest('transaction_date')->paginate(15)->withQueryString();
        $accounts = FinanceAccount::where('is_active', true)->get();

        $income = FinanceTransaction::where('type', 'PENERIMAAN')->where('status', 'POSTED')->sum('amount');
        $expense = FinanceTransaction::where('type', 'PENGELUARAN')->where('status', 'POSTED')->sum('amount');
        $balance = $income - $expense;

        return view('finance.index', compact('transactions', 'accounts', 'income', 'expense', 'balance'));
    }

    public function create()
    {
        $accounts = FinanceAccount::where('is_active', true)->get();
        return view('finance.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'account_id' => 'required|exists:finance_accounts,id',
            'type' => 'required|in:PENERIMAAN,PENGELUARAN,MUTASI',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string',
            'recipient_or_payer' => 'nullable|string|max:150',
            'payment_method' => 'required|in:TUNAI,TRANSFER',
            'spj_number' => 'nullable|string|max:80',
        ]);

        $village = Village::first();
        $trxCount = FinanceTransaction::whereYear('transaction_date', date('Y'))->count() + 1;
        $trxNumber = 'TRX-' . date('Y') . '-' . str_pad($trxCount, 4, '0', STR_PAD_LEFT);

        $validated['village_id'] = $village->uuid ?? Str::uuid()->toString();
        $validated['transaction_number'] = $trxNumber;
        $validated['status'] = 'POSTED';

        $validated['posted_by'] = Auth::id();
        $validated['posted_at'] = now();
        $trx = FinanceTransaction::create($validated);

        AuditService::log('CREATE', 'finance_transactions', $trx->uuid, null, $trx->toArray());

        return redirect()->route('finance.index')->with('success', "Transaksi {$trxNumber} berhasil dicatat.");
    }

    public function accounts()
    {
        $accounts = FinanceAccount::with('parent')->get();
        return view('finance.accounts', compact('accounts'));
    }

    public function storeAccount(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:30|unique:finance_accounts,code',
            'name' => 'required|string|max:150',
            'type' => 'required|in:PENDAPATAN,BELANJA,PEMBIAYAAN,KAS',
            'parent_id' => 'nullable|exists:finance_accounts,id',
        ]);

        $village = Village::first();
        $validated['village_id'] = $village->uuid ?? null;

        FinanceAccount::create($validated);

        return back()->with('success', 'Kode Rekening Akun APBDes berhasil ditambahkan.');
    }

    public function budgets(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $accounts = FinanceAccount::whereIn('type', ['PENDAPATAN', 'BELANJA', 'PEMBIAYAAN'])->get();
        $budgets = FinanceBudget::where('fiscal_year', $year)->with('account')->get()->keyBy('account_id');

        return view('finance.budgets', compact('year', 'accounts', 'budgets'));
    }

    public function storeBudget(Request $request)
    {
        $year = $request->input('fiscal_year', date('Y'));
        $budgets = $request->input('budgets', []);

        $village = Village::first();

        foreach ($budgets as $accountId => $amount) {
            FinanceBudget::updateOrCreate(
                [
                    'fiscal_year' => $year,
                    'account_id' => $accountId,
                ],
                [
                    'village_id' => $village->uuid ?? Str::uuid()->toString(),
                    'budgeted_amount' => $amount ?? 0,
                ]
            );
        }

        return back()->with('success', "Anggaran APBDes Tahun {$year} berhasil disimpan.");
    }

    public function createSpj(Request $request)
    {\n        $transaction = FinanceTransaction::with('account')->where('id',$request->input('transaction_id'))->firstOrFail();\n        return view('finance.spj-create', compact('transaction'));\n    }\n\n    public function storeSpj(Request $request)
    {\n        $data=$request->validate(['transaction_id'=>'required|exists:finance_transactions,id','date'=>'required|date','activity_name'=>'required|string|max:200','description'=>'nullable|string']);\n        $data['village_id']=Village::first()?->uuid; $data['spj_number']='SPJ-'.date('Y').'-'.str_pad((string)(Spj::whereYear('date',date('Y'))->count()+1),4,'0',STR_PAD_LEFT); $data['status']='SUBMITTED'; $data['prepared_by']=Auth::id();\n        $spj=Spj::create($data); FinanceTransaction::whereKey($data['transaction_id'])->update(['spj_number'=>$spj->spj_number]);\n        AuditService::log('CREATE','spj',$spj->uuid,null,$spj->toArray());\n        return redirect()->route('finance.spj')->with('success','SPJ berhasil dibuat.');\n    }\n\n    public function resolveSpj(Request $request,string $uuid)
    {\n        $spj=Spj::where('uuid',$uuid)->firstOrFail(); $status=$request->input('status');\n        abort_unless(in_array($status,['VERIFIED','REJECTED'],true),422); $spj->update(['status'=>$status,'verified_by'=>Auth::id(),'verified_at'=>now()]);\n        return back()->with('success','Status SPJ diperbarui.');\n    }\n\n    public function spjReport(Request $request)
    {
        $transactions = FinanceTransaction::with('account')->whereNotNull('spj_number')->latest()->get();
        return view('finance.spj', compact('transactions'));
    }
}
