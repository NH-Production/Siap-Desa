$ErrorActionPreference = "Stop"

$Root = Split-Path -Parent $PSScriptRoot
$Data = Join-Path $env:ProgramData "SIAP-DESA"
$MySql = Join-Path $Root "runtime\mysql\bin\mysqld.exe"
$MySqlIni = Join-Path $Root "runtime\mysql\my.ini"

New-Item -ItemType Directory -Force -Path $Data | Out-Null
New-Item -ItemType Directory -Force -Path (Join-Path $Data "database") | Out-Null

# MySQL service installation is intentionally guarded so the installer can be
# rerun safely. The actual packaged MySQL distribution supplies mysqld.exe.
if (Test-Path $MySql) {
    & $MySql --install "SIAP-DESA-MySQL" --defaults-file="$MySqlIni" 2>$null
}

# Web and sync services are installed by the final Windows service wrapper
# package used by the release pipeline.
Write-Host "SIAP-DESA runtime directories and database service prepared."
