param(
    [string]$Version = "1.0.0",
    [string]$OutputDir = ".\\dist"
)

$ErrorActionPreference = "Stop"

New-Item -ItemType Directory -Force -Path $OutputDir | Out-Null

Write-Host "Preparing SIAP-DESA installer $Version"

# This script prepares the release staging directory.
# The final EXE is produced by the selected Windows installer compiler.
$stage = Join-Path $OutputDir "SIAP-DESA-$Version"
New-Item -ItemType Directory -Force -Path $stage | Out-Null

$exclude = @(
    ".git",
    ".github",
    "dist",
    "node_modules",
    "vendor"
)

Get-ChildItem -Force . | Where-Object { $exclude -notcontains $_.Name } |
    Copy-Item -Destination $stage -Recurse -Force

Write-Host "Staging completed: $stage"
Write-Host "Next: bundle PHP/MySQL runtime and compile with the project's Windows installer definition."
