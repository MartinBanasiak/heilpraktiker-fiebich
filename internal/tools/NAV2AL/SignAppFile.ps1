$appPackageFileNames = Get-ChildItem -Path "C:\gitRepos\navShippingOptions" -Filter "*.app"
foreach ($appPackageFileName in $appPackageFileNames) {
    Write-Host "Signing APP package $($appPackageFileName.Name)..."
    & "C:\Program Files (x86)\Windows Kits\10\bin\x64\signtool.exe" sign /debug /a /t http://timestamp.verisign.com/scripts/timestamp.dll "$($appPackageFileName.FullName)"
}