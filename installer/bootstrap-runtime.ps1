$ErrorActionPreference = "Stop"

$Root = Split-Path -Parent $PSScriptRoot
$Php = Join-Path $Root "runtime\php\php.exe"
$Artisan = Join-Path $Root "artisan"

if (!(Test-Path $Php)) { throw "PHP runtime tidak ditemukan: $Php" }
if (!(Test-Path $Artisan)) { throw "Laravel artisan tidak ditemukan: $Artisan" }

$Data = Join-Path $env:ProgramData "SIAP-DESA"
New-Item -ItemType Directory -Force -Path $Data | Out-Null

$env:SIAP_DATA_PATH = $Data

Write-Host "Menjalankan migrasi database SIAP-DESA..."
& $Php $Artisan migrate --force

if ($LASTEXITCODE -ne 0) {
    throw "Migrasi database gagal dengan exit code $LASTEXITCODE."
}

Write-Host "Database SIAP-DESA siap."
