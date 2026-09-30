using System.Diagnostics;
using System.IO;
using System.Windows;

namespace SiapDesa.Desktop;

public partial class App : Application
{
    private Process? _web;

    protected override void OnStartup(StartupEventArgs e)
    {
        base.OnStartup(e);
        Directory.CreateDirectory(DataPaths.Root);

        var php = DataPaths.RuntimePath(@"php\php.exe");
        var publicPath = DataPaths.AppPath("public");

        _web = Process.Start(new ProcessStartInfo
        {
            FileName = php,
            Arguments = $"-S 127.0.0.1:8088 -t \"{publicPath}\"",
            WorkingDirectory = DataPaths.AppRoot,
            UseShellExecute = false,
            CreateNoWindow = true
        });

        var window = new MainWindow();
        window.Show();
    }

    protected override void OnExit(ExitEventArgs e)
    {
        try
        {
            if (_web is { HasExited: false })
                _web.Kill(true);
        }
        catch { }

        base.OnExit(e);
    }
}

public static class DataPaths
{
    public static string Root =>
        Environment.GetEnvironmentVariable("SIAP_DATA_PATH")
        ?? @"C:\ProgramData\SIAP Desa";

    public static string AppRoot =>
        AppContext.BaseDirectory.TrimEnd(Path.DirectorySeparatorChar);

    public static string AppPath(string relative) =>
        Path.Combine(AppRoot, relative.Replace('/', Path.DirectorySeparatorChar));

    public static string RuntimePath(string relative) =>
        Path.Combine(AppRoot, "runtime", relative.Replace('/', Path.DirectorySeparatorChar));
}
