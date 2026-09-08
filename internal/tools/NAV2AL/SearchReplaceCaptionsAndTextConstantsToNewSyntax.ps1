$filePath = "C:\gitRepos\navcouponmodule\NAV\al\"
$filePathBackup = "C:\gitRepos\navcouponmodule\NAV\al\Backup\"
$files =  Get-ChildItem -Path $filePath –Recurse –File -Filter *.al
foreach ($file in $files)
{
    $lastPath = Split-Path $file.DirectoryName -Leaf
    $backupPath = $filePathBackup + $lastPath + '\'
    if (-Not (Test-Path $backupPath)) {
        mkdir $backupPath
    }
    $fileWithPath = $file.FullName
    $bakFileWithPath = $backupPath + $file.Name + '.bak'
    Copy-Item -Path $fileWithPath -Destination $bakFileWithPath -Force
    $var = Get-Content -Path ($fileWithPath) -Encoding UTF8 -Raw
    $var = $var -replace "(.*?)\s{0,1}:\s{0,1}TextConst\s(DEU|ENU)='(.*?)',(DEU|ENU)='(.*?)';",'$1 : Label ''$5''; //$2=''$3'''
    $var = $var -replace "CaptionML\s{0,1}=\s{0,1}(DEU|ENU)='(.*?)',\r\n\s+(DEU|ENU)='(.*?)';",'Caption = ''$4''; //$1=''$2'''
    $var = $var -replace "InstructionalTextML\s{0,1}=\s{0,1}(DEU|ENU)='(.*?)',\r\n\s+(DEU|ENU)='(.*?)';",'InstructionalText = ''$4''; //$1=''$2'''
    $var = $var -replace "PromotedActionCategoriesML\s{0,1}=\s{0,1}(DEU|ENU)='(.*?)',\r\n\s+(DEU|ENU)='(.*?)';",'PromotedActionCategories = ''$4''; //$1=''$2'''

    $var -replace "ToolTipML\s{0,1}=\s{0,1}(DEU|ENU)='(.*?)',\r\n\s+(DEU|ENU)='(.*?)';",'ToolTip = ''$4''; //$1=''$2''' | Out-File -FilePath $fileWithPath -Encoding utf8 -Force
}