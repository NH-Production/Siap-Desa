using System;
using System.Windows;
using Microsoft.Web.WebView2.Wpf;

namespace SIAPDesa.Shell;

public partial class MainWindow : Window
{
    private readonly string _url;

    public MainWindow(string url)
    {
        InitializeComponent();
        _url = url;
        Loaded += OnLoaded;
    }

    private async void OnLoaded(object sender, RoutedEventArgs e)
    {
        try
        {
            await Browser.EnsureCoreWebView2Async();
            Browser.CoreWebView2.Settings.AreDefaultContextMenusEnabled = false;
            Browser.CoreWebView2.Settings.AreDevToolsEnabled = false;
            Browser.Source = new Uri(_url);
        }
        catch (Exception ex)
        {
            MessageBox.Show(ex.Message, "SIAP-DESA", MessageBoxButton.OK, MessageBoxImage.Error);
            Close();
        }
    }
}
