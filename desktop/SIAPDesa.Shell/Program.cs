using System.Diagnostics;
using System.IO;
using System.Windows;

namespace SIAPDesa.Shell;

public static class Program
{
    [STAThread]
    public static void Main()
    {
        var root = AppContext.BaseDirectory.TrimEnd('\\');
        var php = Path.Combine(root, "runtime", "php", "php.exe");
        var publicPath = Path.Combine(root, "public");

        if (!File.Exists(php))
        {
            MessageBox.Show(
                "Runtime PHP SIAP-DESA tidak ditemukan. Silakan jalankan installer atau repair.",
                "SIAP-DESA",
                MessageBoxButton.OK,
                MessageBoxImage.Error);
            return;
        }

        var process = Process.Start(new ProcessStartInfo
        {
            FileName = php,
            Arguments = $"-S 127.0.0.1:8088 -t \"{publicPath}\"",
            WorkingDirectory = root,
            UseShellExecute = false,
            CreateNoWindow = true
        });

        try
        {
            var app = new Application();
            var window = new MainWindow("http://127.0.0.1:8088");
            app.Run(window);
        }
        finally
        {
            try
            {
                if (process is { HasExited: false })
                    process.Kill(entireProcessTree: true);
            }
            catch { }
        }
    }
}
