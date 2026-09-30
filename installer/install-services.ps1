$ErrorActionPreference = "Stop"

$Root = Split-Path -Parent $PSScriptRoot
$Data = Join-Path $env:ProgramData "SIAP-DESA"
$MySql = Join-Path $Root "runtime\mysql\bin\mysqld.exe"
$MySqlIni = Join-Path $Root "runtime\mysql\my.ini"
$Bootstrap = Join-Path $Root "installer\bootstrap-runtime.ps1"

New-Item -ItemType Directory -Force -Path $Data | Out-Null
New-Item -ItemType Directory -Force -Path (Join-Path $Data "database") | Out-Null

if (Test-Path $MySql) {
    & $MySql --install "SIAP-DESA-MySQL" --defaults-file="$MySqlIni" 2>$null
    Start-Service -Name "SIAP-DESA-MySQL" -ErrorAction SilentlyContinue
    Start-Sleep -Seconds 3
}

if (Test-Path $Bootstrap) {
    & powershell.exe -ExecutionPolicy Bypass -File $Bootstrap
    if ($LASTEXITCODE -ne 0) {
        throw "Bootstrap database SIAP-DESA gagal."
    }
}

Write-Host "SIAP-DESA runtime, database service, dan migration siap."
