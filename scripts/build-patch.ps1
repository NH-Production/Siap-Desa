param(
    [Parameter(Mandatory=$true)][string]$Version,
    [Parameter(Mandatory=$true)][string]$MinimumVersion = "1.0.0",
    [string]$OutputDir = ".\\dist\\patches"
)

$ErrorActionPreference = "Stop"

New-Item -ItemType Directory -Force -Path $OutputDir | Out-Null

$stage = Join-Path $OutputDir "SIAP-DESA-Patch-$Version"
New-Item -ItemType Directory -Force -Path $stage | Out-Null

# Patch packages contain application/runtime updates only.
# ProgramData is deliberately excluded.
$exclude = @(
    ".git",
    ".github",
    "dist",
    "node_modules",
    "vendor",
    "storage",
    "bootstrap\\cache"
)

Get-ChildItem -Force . | Where-Object { $exclude -notcontains $_.Name } |
    Copy-Item -Destination $stage -Recurse -Force

$zip = Join-Path $OutputDir "SIAP-DESA-Patch-$Version.zip"
if (Test-Path $zip) { Remove-Item $zip -Force }

Compress-Archive -Path (Join-Path $stage "*") -DestinationPath $zip -Force
$hash = (Get-FileHash $zip -Algorithm SHA256).Hash

Write-Host "Patch: $zip"
Write-Host "Minimum version: $MinimumVersion"
Write-Host "SHA-256: $hash"
