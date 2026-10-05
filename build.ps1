# Packages bigspy-theme/ into releases/bigspy-theme-<version>.zip.
# Uses tar (bsdtar, built into Windows 10+) because Compress-Archive on
# Windows PowerShell 5.1 writes backslash paths that break on Linux servers.
$ErrorActionPreference = 'Stop'
Set-Location $PSScriptRoot

$style = Get-Content -Raw -Path 'bigspy-theme/style.css'
if ($style -notmatch 'Version:\s*([0-9.]+)') { throw 'Version not found in style.css' }
$version = $Matches[1]

New-Item -ItemType Directory -Force -Path 'releases' | Out-Null
$zip = "releases/bigspy-theme-$version.zip"
if (Test-Path $zip) { Remove-Item $zip -Force }

tar -a -c -f $zip --exclude '.DS_Store' --exclude 'Thumbs.db' 'bigspy-theme'
if ($LASTEXITCODE -ne 0) { throw 'tar failed' }

# Keep only the current theme archive after a successful build.
$releaseRoot = (Resolve-Path -LiteralPath 'releases').Path
Get-ChildItem -LiteralPath $releaseRoot -File | Where-Object {
    $_.Name -match '^bigspy-theme-[0-9]+(?:\.[0-9]+)*\.zip$' -and $_.Name -ne "bigspy-theme-$version.zip"
} | ForEach-Object {
    $archivePath = [System.IO.Path]::GetFullPath($_.FullName)
    if ([System.IO.Path]::GetDirectoryName($archivePath) -ne $releaseRoot) {
        throw 'Archive path is outside the releases directory'
    }
    Remove-Item -LiteralPath $archivePath -Force
}
Write-Host "Created $zip"
