Function Apply-ALTranslationTableToXlfFile
{
    [CmdletBinding()]
    param
    (
        [Parameter(Mandatory=$true, ValueFromPipelineByPropertyname=$true)]
        [hashtable]$TranslationTable, 
        [Parameter(Mandatory=$true, ValueFromPipelineByPropertyname=$true)]
        [String]$XlfFile,
        [Parameter(Mandatory=$true, ValueFromPipelineByPropertyname=$true)]
        [String]$TargetLanguage
    )

    Write-Host "Loading translation file ${XlfFile}..."
    [xml]$Xlf = Get-Content -Path $XlfFile -Encoding UTF8 
    $Xlf.PreserveWhitespace = $true
    $Xlf.DocumentElement.file.SetAttribute('target-language',$TargetLanguage)
    $NoOfAlreadyTranslated = 0
    $NoOfAutomaticallyTranslated = 0
    $NoOfNeedsTranslation = 0
    $NoOfNotToBeTranslated = 0
    foreach ($node in $Xlf.xliff.file.body.group.'trans-unit') {
        if ($node.translate -ieq 'no') {
            Write-Verbose "$($node.source) should not be translated"
            $NoOfNotToBeTranslated ++
        } elseif ($node.translate -ieq 'yes') {
            if ($node.target) {
                Write-Verbose "$($node.source) already translated"
                $NoOfAlreadyTranslated ++
            } else {
                if ($TranslationTable.ContainsKey($node.source)) {
                    Write-Verbose "$($node.source) automatically translated"
                    $target = $Xlf.CreateElement('target', $Xlf.DocumentElement.NamespaceURI)
                    $target.InnerText = $TranslationTable.Item($node.source)
                    $target.SetAttribute('state','translated')
                    $targetNode = $node.InsertBefore($target,$node.note[0])
                    $NoOfAutomaticallyTranslated ++
                } elseif ($Xlf.DocumentElement.file.'source-language'.Substring(0,2) -eq $TargetLanguage.Substring(0,2)) {
                    Write-Verbose "$($node.source) automatically translated"
                    $target = $Xlf.CreateElement('target', $Xlf.DocumentElement.NamespaceURI)
                    $target.InnerText = $node.source
                    $target.SetAttribute('state','translated')
                    $targetNode = $node.InsertBefore($target,$node.note[0])
                    $NoOfAutomaticallyTranslated ++
                } else {
                    Write-Verbose "no translation found for $($node.source)"
                    $target = $Xlf.CreateElement('target', $Xlf.DocumentElement.NamespaceURI)
                    $target.InnerText = ''
                    $target.SetAttribute('state','needs-translation')
                    $targetNode = $node.InsertBefore($target,$node.note[0])
                    $NoOfNeedsTranslation ++
                }
            }
        }
    }
    Write-Host " Already translated: $NoOfAlreadyTranslated `r`n Automatically translated: $NoOfAutomaticallyTranslated `r`n Needs translation: $NoOfNeedsTranslation `r`n Not to be translated: $NoOfNotToBeTranslated"
    Write-Host "Saving translation file ${XlfFile}..."
    $xlf.Save($XlfFile)
}


Function Get-ALTranslationFileName($initialDirectory)
{
    [System.Reflection.Assembly]::LoadWithPartialName("System.windows.forms") | Out-Null
    
    $OpenFileDialog = New-Object System.Windows.Forms.OpenFileDialog
    $OpenFileDialog.Multiselect = $false
    $OpenFileDialog.Title = "Select CAL Translation file..."
    $OpenFileDialog.initialDirectory = $initialDirectory
    $OpenFileDialog.filter = "TXT (*.txt)| *.txt"
    $OpenFileDialog.ShowDialog() | Out-Null
    return $OpenFileDialog.FileName
}


Function Get-ALTranslationTableFromXlf
{
    [CmdletBinding()]
    param
    (
        [Parameter(Mandatory=$true, ValueFromPipelineByPropertyname=$true)]
        [String]$XlfFile,
        [Parameter(Mandatory=$false, ValueFromPipelineByPropertyname=$true)]
        [HashTable]$TranslateTable = @{}
    )

    Write-Host "Loading translation file ${XlfFile}..."
    if (!$TranslateTable) {$TranslateTable = @{}}
    [xml]$Xlf = Get-Content -Path $XlfFile -Encoding UTF8 
    foreach ($node in $Xlf.xliff.file.body.group.'trans-unit') {
        if ($node.translate -ieq 'yes') {
            if ($node.target) {
                if (!($TranslateTable.ContainsKey($node.source))) {
                    if ($node.target.GetType().Name -eq "String") {
                        $TranslateTable.Add($node.source,$node.target)
                    } else {
                        $TranslateTable.Add($node.source,$node.target.'#text')
                    }
                }
            }
        }
    }
    return $TranslateTable
}

Function Get-ALTranslationTableFromCaptions
{
    [CmdletBinding()]
    param
    (
        [Parameter(Mandatory=$true, ValueFromPipelineByPropertyname=$true)]
        [String]$TranslationFile,
        [Parameter(Mandatory=$true, ValueFromPipelineByPropertyname=$true)]
        [String]$LanguageNo,
        [Parameter(Mandatory=$False, ValueFromPipelineByPropertyname=$true)]
        [HashTable]$TranslateTable
    )

    Write-Host "Loading translation data from ${TranslationFile}..."
    
    $TempTransTable = @{}

    $CALTranslateFile = Get-Content -Encoding Oem -Path $TranslationFile
    if ($LanguageNo -eq '3079') {
        $LanguageNo = '1031'
    }
    if ($LanguageNo -eq '2055') {
        $LanguageNo = '1031'
    }
    $LanguageNo = '-A' + $LanguageNo  


    if (!$TranslateTable) {$TranslateTable = @{}}  
    $keyFound = $false
    $NoOfLines = 0
    foreach ($CALTranslateLine in $CALTranslateFile) {   
        $key = $CALTranslateLine.Split(':')[0]
        if ($key -match '-A1033') {        
            $value = $CALTranslateLine.Split(':')[1]
            if (-not ($TempTransTable.ContainsKey($key))) {
                $TempTransTable.Add($key,$value)
            }
        }
    }

    foreach ($CALTranslateLine in $CALTranslateFile) {
        $key = $CALTranslateLine.Split(':')[0]
        if ($key -match $LanguageNo) {
            $value = $CALTranslateLine.Split(':')[1]
            $translateKey = $key -replace ($LanguageNo,'-A1033');
            if ($TempTransTable.ContainsKey($translateKey)) {
                if (-not ($TranslateTable.ContainsKey($TempTransTable[$translateKey]))) {
                    $TranslateTable.Add($TempTransTable[$translateKey],$value)
                    $NoOfLines++
                }
            }
        }
    }
    

    Write-Host "${NoOfLines} lines loaded in memory!"
    return $TranslateTable    
}




$defaultTranslationFolder = "C:\gitRepos\navcouponmodule\NAV\al\Translations"
$TranslationFolderQuestion = "Please enter the directory-path where finsql (NAV2018) is to be found (Empty means '`"$defaultTranslationFolder`"')";
$TranslationFolder = Read-Host $TranslationFolderQuestion;

if ($TranslationFolder -eq "") {
    $TranslationFolder = $defaultTranslationFolder;
}

$defaultLanguageName = "it-CH,fr-CH,de-AT,de-CH,en-US,de-DE,en-GB"
$LanguageNameQuestion = "Please enter the language codes comma seperated (Empty means '`"$defaultLanguageName`"')";
$LanguageName = Read-Host $LanguageNameQuestion;

if ($LanguageName -eq "") {
    $LanguageName = $defaultLanguageName;
}

$LanguageName = $LanguageName.Split(',')

$TranslationSource = Get-ChildItem -Path $TranslationFolder -Filter '*.g.xlf' -ErrorAction SilentlyContinue
if ([string]::IsNullOrEmpty($TranslationSource)) {
    Write-Host -ForegroundColor Red "Unable to locate a translation source file in folder ${TranslationFolder}"
    throw
}

foreach ($Language in $LanguageName) {

    $TranslationTable = @{}
    
    if ([string]::IsNullOrEmpty($LanguageName)) {
        Write-Host -ForegroundColor Red "Language not found or selected!"
        throw
    }

    $CultureInfos = [System.Globalization.Cultureinfo]::GetCultures("AllCultures")

    $LanguageId = ($CultureInfos | Where-Object -Property Name -ieq $Language).LCID
    Write-host $LanguageId

    $TranslationTarget = ($TranslationSource.FullName).Replace(".g.xlf",".${Language}.xlf")
    if (Test-Path $TranslationTarget) {
        $TranslationTable = Get-ALTranslationTableFromXlf -XlfFile $TranslationTarget -TranslateTable $TranslationTable
        Remove-Item -Path $TranslationTarget -Force    
    }

    Write-Host "Select C/AL translation file to import..."
    $CALTranslationFile = Get-ALTranslationFileName -initialDirectory $TranslationFolder

    if ($CALTranslationFile) {
        if (Test-Path $CALTranslationFile) {
            $TranslationTable = Get-ALTranslationTableFromCaptions -TranslationFile $CALTranslationFile -LanguageNo $LanguageId -TranslateTable $TranslationTable
        }
    }

    if ($TranslationTable) {
        Copy-Item -Path $TranslationSource.FullName -Destination $TranslationTarget 
        Apply-ALTranslationTableToXlfFile -TranslationTable $TranslationTable -XlfFile $TranslationTarget -TargetLanguage $Language 
    } else {
        $TranslationTable = @{}
        Copy-Item -Path $TranslationSource.FullName -Destination $TranslationTarget 
        Apply-ALTranslationTableToXlfFile -TranslationTable $TranslationTable -XlfFile $TranslationTarget -TargetLanguage $Language 
    }
}