using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Windows.Forms;
using System.Runtime.InteropServices;
using System.ComponentModel;

namespace IPSToolLibrary
{
    class MonitorUtils
    {
        [DllImport("user32.dll", EntryPoint = "GetDesktopWindow")]
        private static extern IntPtr GetDesktopWindow();

        [DllImport("user32.dll")]
        private static extern IntPtr SendMessage(IntPtr hWnd, uint Msg, int wParam, int lParam);

        [DllImport("user32.dll")]
        private static extern int FindWindow(string className, string windowText);

        [DllImport("user32.dll")]
        private static extern int ShowWindow(int hwnd, int command);



        [DllImport("user32.dll")]
        private static extern bool SystemParametersInfo(int uAction, int uParam, ref bool lpvParam, int flags);

        [DllImport("user32.dll")]
        private static extern IntPtr OpenDesktop(string hDesktop, int Flags, bool Inherit, uint DesiredAccess);

        [DllImport("user32.dll")]
        private static extern bool CloseDesktop(IntPtr hDesktop);

        [DllImport("user32.dll")]
        private static extern bool EnumDesktopWindows(IntPtr hDesktop, EnumDesktopWindowsProc callback, IntPtr lParam);

        [DllImport("user32.dll")]
        private static extern bool IsWindowVisible(IntPtr hWnd);
        
        [DllImport("user32.dll")]
        private static extern IntPtr GetForegroundWindow();

        [DllImport("user32.dll")]
        private static extern int PostMessage(IntPtr hWnd, int wMsg, int wParam, int lParam);

        private delegate bool EnumDesktopWindowsProc(IntPtr hDesktop, IntPtr lParam);

        private const int WM_SYSCOMMAND = 0x0112;

        private const int SC_SCREENSAVER = 0xF140;
        private const int SC_MONITORPOWER = 0xF170;

        private const int MONITOR_ON = -1;
        private const int MONITOR_OFF = 2;
        private const int MONITOR_STANBY = 1;

        private const int TASKBAR_HIDE = 0;
        private const int TASKBAR_SHOW = 1;

        private const int SPI_GETSCREENSAVERRUNNING = 114;
        private const uint DESKTOP_WRITEOBJECTS = 0x0080;
        private const uint DESKTOP_READOBJECTS = 0x0001;
        private const int WM_CLOSE = 16;


        private IntPtr handle;

        public MonitorUtils(IntPtr gui)
        {
            handle = gui;
        }

        public void StartScreenSaver()
        {
            SendMessage(GetDesktopWindow(), WM_SYSCOMMAND, SC_SCREENSAVER, 0);
        }

        public void StopScreenSaver()
        {
            bool isActive = false;
            SystemParametersInfo(SPI_GETSCREENSAVERRUNNING, 0, ref isActive, 0);
            if (isActive)
            {
                IntPtr hDesktop = OpenDesktop("Screen-saver", 0, false, DESKTOP_READOBJECTS | DESKTOP_WRITEOBJECTS);
                if (hDesktop != IntPtr.Zero)
                {
                    EnumDesktopWindows(hDesktop, new EnumDesktopWindowsProc(KillScreenSaverFunc), IntPtr.Zero);
                    CloseDesktop(hDesktop);

                }
                else
                {
                    PostMessage(GetForegroundWindow(), WM_CLOSE, 0, 0);

                }
            }
        }

        private static bool KillScreenSaverFunc(IntPtr hWnd, IntPtr lParam)
        {
            if (IsWindowVisible(hWnd)) PostMessage(hWnd, WM_CLOSE, 0, 0);
            return true;
        }

        
        public void ScreenPowerOff()
        {
            SendMessage(handle, WM_SYSCOMMAND, SC_MONITORPOWER, MONITOR_OFF);
        }

        public void ScreenPowerOn()
        {
            SendMessage(handle, WM_SYSCOMMAND, SC_MONITORPOWER, MONITOR_ON);
        }
    }
}
