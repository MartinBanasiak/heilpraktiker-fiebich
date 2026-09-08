#Check for admin privileges and elevate if necessary
if (-not ([Security.Principal.WindowsPrincipal][Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator))
{
    $arguments = "& '" + $myinvocation.mycommand.definition + "'"
    Start-Process powershell -Verb runAs -ArgumentList $arguments
    Break
}

function appendSlashIfNeeded([REF]$pathString)
{
    if (!($pathString.Value.EndsWith("/") -or ($pathString.Value.EndsWith("\")))) {
        $pathString.Value += "\";
    }
}
$userMysydeVersionFilter = "";
$defaultMysydeVersionFilterExp = "*@MYSW1*";
$defaultMysydeVersionFilter = "Version List=`"$defaultMysydeVersionFilterExp`""

$defaultAppObjectsIDFilter = "ID=1057|5447300..5447449"
$defaultNAVObjectsIDFilter = "ID=..1056|1060..5447299|5447557.."

$navModelToolsScript = "Microsoft.Dynamics.Nav.Model.Tools.psd1";
$navAdminToolsScript = "NavAdminTool.ps1";

$userServer = "";
$server = "dev01";

$userServiceName = "";
$serviceName = "DC-2018-DEV";

$userDatabaseName = "";
$databaseName = "DC-2018-DEV";

$userFinSqlPath = "";
$defaultFinSqlPath = "${env:ProgramFiles(x86)}\Microsoft Dynamics NAV\110\RoleTailored Client\";
$finSqlPath = "";

$userServicePath = "";
$defaultServicePath = "${env:ProgramFiles}\Microsoft Dynamics NAV\110\Service\";
$servicePath = "";

$userTxtSavePath = "";
$defaultTxtSavePath = "C:\dc2018\internal\NAV\txt\";
$txtSavePath = "";

$individualObjectsSubPath = "IndividualObjects";
$allObjectExportFileName = "AllObjects.txt";

$userFobSavePath = "";
$defaultFobSavePath = "C:\dc2018\internal\NAV\fob\";
$fobSavePath = "";

$defaultObjectsFileName = "defaultObjects.fob"
$appObjectsFileName = "appObjects.fob"

$captionsSubPath = "IndividualCaptions";
$captionsFileName = "Captions.txt";


#Read user input
$userFinSqlPathQuestion = "Please enter the directory-path where finsql (NAV2018) is to be found (Empty means '`"$defaultFinSqlPath`"')";
$userFinSqlPath = Read-Host $userFinSqlPathQuestion;
$userServicePathQuestion = "Please enter the directory-path where the NAV Service (NAV2018) is to be found (Empty means '`"$defaultServicePath`"')"
$userServicePath = Read-Host $userServicePathQuestion;
$userServerQuestion = "Please enter the name of the Server where the Dynamics NAV Service is running (Empty means '`"$server`"')";
$userServer = Read-Host $userServerQuestion;
$userServiceNameQuestion = "Please enter the name of target the Dynamics NAV Service (Empty means '`"$serviceName`"')";
$userServiceName = Read-Host $userServiceNameQuestion;
$userDatabaseNameQuestion = "Please enter the name of the NAV-Database from which to export (Empty means '`"$databaseName`"')";
$userDatabaseName = Read-Host $userDatabaseNameQuestion;


$userMysydeVersionFilterQuestion = "For Text-Export: Should Objects be filtered? Options: 1)Yes, filter to include only Mysyde Objects 2)Yes, let me enter a filter expression 3)No";
$userVersionFilterExpressionQuestion = "Please enter your filter-expression:";
$userMysydeVersionFilter = Read-Host $userMysydeVersionFilterQuestion;
while("1","2","3" -notcontains $userMysydeVersionFilter)
{
	$userMysydeVersionFilter = Read-Host "$userMysydeVersionFilterQuestion"
}
switch($userMysydeVersionFilter)
{
    "1" {$userMysydeVersionFilter = $defaultMysydeVersionFilter}
    "2" {
        $userFilterExp = Read-Host $userVersionFilterExpressionQuestion;
        $userMysydeVersionFilter = "Version List=`"" + $userFilterExp + "`"";
        }
    "3" {
        $userMysydeVersionFilter = "";        
        }
    default {
        $userMysydeVersionFilter = "";
        }

    
}
$userTxtSavePath = Read-Host "Please enter the directory-path where objects exported as TXT are to be stored (empty means '"$defaultTxtSavePath"')";
$userFobSavePath = Read-Host "Please enter the directory-path where objects exported as FOB are to be stored (empty means '"$defaultFobSavePath"')";


#Prepare Strings
if ($userFinSqlPath -eq "") {
    $finSqlPath = $defaultFinSqlPath;
} else {
    $finSqlPath = $userFinSqlPath;
}

if ($userServicePath -eq "") {
    $servicePath = $defaultServicePath;
} else {
    $servicePath = $userServicePath;
}

if ($userServer -eq "") {
    $server = $server;
} else {
    $server = $userServer;
}

if ($userServiceName -eq "") {
    $serviceName = $serviceName;
} else {
    $serviceName = $userServiceName;
}

if ($userDatabaseName -eq "") {
    $databaseName = $databaseName;
} else {
    $databaseName = $userDatabaseName;
}

if ($userTxtSavePath -eq "") {
    $txtSavePath = $defaultTxtSavePath;
} else {
    $txtSavePath = $userTxtSavePath;
}

if ($userFobSavePath -eq "") {
    $fobSavePath = $defaultFobSavePath;
} else {
    $fobSavePath = $userFobSavePath;
}

appendSlashIfNeeded([REF]$finSqlPath);
appendSlashIfNeeded([REF]$servicePath);
appendSlashIfNeeded([REF]$txtSavePath);


#Import Modules
$navModelToolsPath = $finSqlPath + $navModelToolsScript;
$navAdminToolsPath = $servicePath + $navAdminToolsScript;

Import-Module $navModelToolsPath -WarningAction SilentlyContinue | Out-Null;
Import-Module $navAdminToolsPath -WarningAction SilentlyContinue | Out-Null;

#Check continue
$title = "Exporting NAV Objects to Folder";
$message = "All existing Files will be deleted. Do you wish to continue?";

$yes = New-Object System.Management.Automation.Host.ChoiceDescription "&Yes", `
    "Continues and deletes existing NAV object-export-files.";

$no = New-Object System.Management.Automation.Host.ChoiceDescription "&No", `
    "Aborts the process.";

$options = [System.Management.Automation.Host.ChoiceDescription[]]($yes, $no);

$continue = $host.ui.PromptForChoice($title, $message, $options, 0);

if ($continue.Equals(0)) {

    Write-Host "Proceeding with export...";

    $individualObjectsPath = $txtSavePath + $individualObjectsSubPath;

    if (Test-Path $individualObjectsPath) {
        Get-ChildItem -Path $individualObjectsPath -Include *.* -File -Recurse | foreach { $_.Delete()};
    } else {
        mkdir $individualObjectsPath;
    }

    $allObjectsSavePath = $txtSavePath + $allObjectExportFileName;
    if($userMysydeVersionFilter.Equals(""))
    {
        Export-NAVApplicationObject -DatabaseServer $server -DatabaseName $databaseName -Path $allObjectsSavePath -Force | Out-Null;
    } else {
        Export-NAVApplicationObject -DatabaseServer $server -DatabaseName $databaseName -Path $allObjectsSavePath -Filter $userMysydeVersionFilter -Force | Out-Null;
    }

    appendSlashIfNeeded([REF]$txtSavePath);
    $captionPath = $txtSavePath + $captionsSubPath;
    if (Test-Path $captionPath) {
        Get-ChildItem -Path $captionPath -Include *.* -File -Recurse | foreach { $_.Delete()};
    } else {
        mkdir $captionPath;
    }
    Export-NAVApplicationObjectLanguage -Source $allObjectsSavePath -Destination $captionPath -Encoding UTF8;
    
    Split-NAVApplicationObjectFile -Source $allObjectsSavePath -Destination $individualObjectsPath | Out-Null;
    
    #Check if created
    $directoryInfo = Get-ChildItem $individualObjectsPath | Measure-Object;
    $fileCount = $directoryInfo.Count;

    if ($fileCount -gt 0) {
        appendSlashIfNeeded([REF]$txtSavePath);
        $captionPath = $txtSavePath + $captionsFileName;
        Export-NAVApplicationObjectLanguage -Source $allObjectsSavePath -Destination $captionPath -Encoding UTF8;
        Write-Host "Exported successfully to $individualObjectsPath";
    } else {
        Write-Host "Could not export";
    }
	
	Write-Host "Attempting FOB-Export...";

	if (Test-Path $fobSavePath) {
        Get-ChildItem -Path $fobSavePath -Include *.* -File -Recurse | foreach { $_.Delete()};
    } else {
        mkdir $fobSavePath;
    }	
	
	$defaultObjFilterString = $defaultNAVObjectsIDFilter + ";" + $defaultMysydeVersionFilter
	$defaultObjectsFilePath = $fobSavePath + $defaultObjectsFileName
	$appObjFilterString = $defaultAppObjectsIDFilter + ";" + $defaultMysydeVersionFilter
	$appObjectsFilePath = $fobSavePath + $appObjectsFileName
    Export-NAVApplicationObject -DatabaseServer $server -DatabaseName $databaseName -Path $defaultObjectsFilePath -Filter $defaultObjFilterString -Force | Out-Null;
    Export-NAVApplicationObject -DatabaseServer $server -DatabaseName $databaseName -Path $appObjectsFilePath -Filter $appObjFilterString -Force | Out-Null;
    
	#Check if created
    if (Test-Path $defaultObjectsFilePath) {
        Write-Host "Exported default NAV objects successfully to " + $defaultObjectsFilePath + ".";
    } else {
        Write-Host "Could not export default NAV objects.";
    }
	if (Test-Path $appObjectsFilePath) {
        Write-Host "Exported app objects successfully to " + $appObjectsFilePath + ".";
    } else {
        Write-Host "Could not export app objects.";
    }
	if (Test-Path $captionPath) {
		Write-Host "Exported captions successfully to " + $captionPath + ".";		
	} else {
		Write-Host "Could not export captions.";
	}
} else {
    Write-Host "User aborted";
}

