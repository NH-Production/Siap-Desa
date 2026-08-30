Add-Type -TypeDefinition @'
using System;
using System.Diagnostics;
using System.Drawing;
using System.IO;
using System.Net;
using System.Threading;
using System.Windows.Forms;

namespace SiapDesa
{
    public class Program
    {
        private static Process phpProcess;
        private static NotifyIcon trayIcon;
        private static string appUrl = "http://127.0.0.1:8088";
        private static string baseDir;
        private static string phpExe;
        private static string dataDir = @"C:\ProgramData\SIAP Desa";

        [STAThread]
        public static void Main(string[] args)
        {
            Application.EnableVisualStyles();
            Application.SetCompatibleTextRenderingDefault(false);

            baseDir = AppDomain.CurrentDomain.BaseDirectory;
            if (File.Exists(Path.Combine(baseDir, "..", "runtime", "php", "php.exe")))
            {
                baseDir = Path.GetFullPath(Path.Combine(baseDir, ".."));
            }

            phpExe = Path.Combine(baseDir, "runtime", "php", "php.exe");

            if (!File.Exists(phpExe))
            {
                MessageBox.Show("PHP Runtime tidak ditemukan di: " + phpExe, "SIAP Desa Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
                return;
            }

            // Ensure ProgramData structure
            EnsureDirectories();

            // Run database migration if first run
            RunDatabaseInit();

            // Start PHP Background Server
            StartPhpServer();

            // Wait for server health
            WaitForServer();

            // Open default browser
            Process.Start(new ProcessStartInfo(appUrl) { UseShellExecute = true });

            // Initialize System Tray
            InitializeTray();

            Application.Run();
        }

        private static void EnsureDirectories()
        {
            try
            {
                Directory.CreateDirectory(Path.Combine(dataDir, "database"));
                Directory.CreateDirectory(Path.Combine(dataDir, "storage"));
                Directory.CreateDirectory(Path.Combine(dataDir, "backups"));
                Directory.CreateDirectory(Path.Combine(dataDir, "logs"));
            }
            catch { }
        }

        private static void RunDatabaseInit()
        {
            string sqliteDb = Path.Combine(dataDir, "database", "siap_local.sqlite");
            if (!File.Exists(sqliteDb))
            {
                File.Create(sqliteDb).Close();
                try
                {
                    ProcessStartInfo psi = new ProcessStartInfo
                    {
                        FileName = phpExe,
                        Arguments = "artisan migrate --seed --force",
                        WorkingDirectory = baseDir,
                        WindowStyle = ProcessWindowStyle.Hidden,
                        CreateNoWindow = true,
                        UseShellExecute = false
                    };
                    Process p = Process.Start(psi);
                    p.WaitForExit(30000);
                }
                catch { }
            }
        }

        private static void StartPhpServer()
        {
            try
            {
                string publicDir = Path.Combine(baseDir, "public");
                ProcessStartInfo psi = new ProcessStartInfo
                {
                    FileName = phpExe,
                    Arguments = string.Format("-S 127.0.0.1:8088 -t \"{0}\"", publicDir),
                    WorkingDirectory = baseDir,
                    WindowStyle = ProcessWindowStyle.Hidden,
                    CreateNoWindow = true,
                    UseShellExecute = false
                };
                phpProcess = Process.Start(psi);
            }
            catch (Exception ex)
            {
                MessageBox.Show("Gagal menjalankan server lokal: " + ex.Message, "SIAP Desa Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
            }
        }

        private static void WaitForServer()
        {
            for (int i = 0; i < 15; i++)
            {
                try
                {
                    HttpWebRequest req = (HttpWebRequest)WebRequest.Create(appUrl + "/up");
                    req.Timeout = 1500;
                    using (HttpWebResponse res = (HttpWebResponse)req.GetResponse())
                    {
                        if (res.StatusCode == HttpStatusCode.OK) break;
                    }
                }
                catch
                {
                    Thread.Sleep(500);
                }
            }
        }

        private static void InitializeTray()
        {
            trayIcon = new NotifyIcon();
            trayIcon.Text = "SIAP Desa v1.0.0 (Local-First Active)";
            trayIcon.Icon = SystemIcons.Application;
            trayIcon.Visible = true;

            ContextMenuStrip menu = new ContextMenuStrip();
            menu.Items.Add("Buka Aplikasi SIAP Desa", null, (s, e) => {
                Process.Start(new ProcessStartInfo(appUrl) { UseShellExecute = true });
            });
            menu.Items.Add(new ToolStripSeparator());
            menu.Items.Add("Diagnostik Sistem", null, (s, e) => {
                Process.Start(new ProcessStartInfo(appUrl + "/system/diagnostics") { UseShellExecute = true });
            });
            menu.Items.Add("Backup Database", null, (s, e) => {
                Process.Start(new ProcessStartInfo(appUrl + "/backup") { UseShellExecute = true });
            });
            menu.Items.Add(new ToolStripSeparator());
            menu.Items.Add("Keluar & Hentikan Aplikasi", null, (s, e) => {
                Shutdown();
            });

            trayIcon.ContextMenuStrip = menu;
            trayIcon.DoubleClick += (s, e) => {
                Process.Start(new ProcessStartInfo(appUrl) { UseShellExecute = true });
            };

            trayIcon.ShowBalloonTip(3000, "SIAP Desa Berjalan", "Aplikasi aktif di localhost:8088 (Local-First Ready)", ToolTipIcon.Info);
        }

        private static void Shutdown()
        {
            try
            {
                if (trayIcon != null)
                {
                    trayIcon.Visible = false;
                    trayIcon.Dispose();
                }
                if (phpProcess != null && !phpProcess.HasExited)
                {
                    phpProcess.Kill();
                }
            }
            catch { }
            finally
            {
                Application.Exit();
            }
        }
    }
}
'@ -ReferencedAssemblies "System.Windows.Forms", "System.Drawing", "System.Net" -OutputAssembly "C:\Users\NH PRODUCTION\.gemini\antigravity\scratch\siap-desa\SIAP_Desa_Launcher.exe" -OutputType WindowsApplication
