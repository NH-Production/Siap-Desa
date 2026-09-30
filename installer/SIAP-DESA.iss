; SIAP-DESA full installer skeleton
; Build with Inno Setup on Windows.
#define MyAppName "SIAP-DESA"
#define MyAppVersion "1.0.0"
#define MyAppPublisher "NH Production"
#define MyAppExeName "SIAP-DESA.exe"

[Setup]
AppId={{C1C1E3E0-8B1C-4C89-9F0D-9D7D2D6F9A01}
AppName={#MyAppName}
AppVersion={#MyAppVersion}
AppPublisher={#MyAppPublisher}
DefaultDirName={autopf}\SIAP-DESA
DefaultGroupName=SIAP-DESA
OutputDir=..\dist
OutputBaseFilename=SIAP-DESA-Setup-{#MyAppVersion}
Compression=lzma
SolidCompression=yes
PrivilegesRequired=admin
ArchitecturesInstallIn64BitMode=x64compatible
WizardStyle=modern

[Files]
Source: "..\desktop\SIAPDesa.Shell\bin\Release\net8.0-windows\publish\*"; DestDir: "{app}"; Flags: recursesubdirs ignoreversion
Source: "..\runtime\*"; DestDir: "{app}\runtime"; Flags: recursesubdirs ignoreversion
Source: "..\app\*"; DestDir: "{app}\app"; Flags: recursesubdirs ignoreversion
Source: "..\public\*"; DestDir: "{app}\public"; Flags: recursesubdirs ignoreversion

[Dirs]
Name: "{commonappdata}\SIAP-DESA"
Name: "{commonappdata}\SIAP-DESA\database"
Name: "{commonappdata}\SIAP-DESA\uploads"
Name: "{commonappdata}\SIAP-DESA\documents"
Name: "{commonappdata}\SIAP-DESA\backups"
Name: "{commonappdata}\SIAP-DESA\logs"
Name: "{commonappdata}\SIAP-DESA\config"

[Icons]
Name: "{group}\SIAP-DESA"; Filename: "{app}\{#MyAppExeName}"
Name: "{commondesktop}\SIAP-DESA"; Filename: "{app}\{#MyAppExeName}"

[Run]
Filename: "powershell.exe"; Parameters: "-ExecutionPolicy Bypass -File ""{app}\installer\install-services.ps1"""; Flags: runhidden waituntilterminated
Filename: "{app}\{#MyAppExeName}"; Description: "Jalankan SIAP-DESA"; Flags: nowait postinstall skipifsilent
