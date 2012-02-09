/**
 * This file is part of the IPSLibrary.
 *
 * The IPSLibrary is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * The IPSLibrary is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with the IPSLibrary. If not, see http://www.gnu.org/licenses/gpl.txt.
 */
 
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

        private const int WM_SYSCOMMAND = 0x0112;

        private const int SC_SCREENSAVER = 0xF140;
        private const int SC_MONITORPOWER = 0xF170;

        private const int MONITOR_ON = -1;
        private const int MONITOR_OFF = 2;
        private const int MONITOR_STANBY = 1;

        private const int TASKBAR_HIDE = 0;
        private const int TASKBAR_SHOW = 1;

        private IntPtr handle;

        public MonitorUtils(IntPtr gui)
        {
            handle = gui;
        }

        public void StartScreenSaver()
        {
            SendMessage(GetDesktopWindow(), WM_SYSCOMMAND, SC_SCREENSAVER, 0);
        }

        public void ScreenPowerOff()
        {
            SendMessage(handle, WM_SYSCOMMAND, SC_MONITORPOWER, MONITOR_OFF);
        }

        public void ScreenPowerOn()
        {
            SendMessage(handle, WM_SYSCOMMAND, SC_MONITORPOWER, MONITOR_ON);
        }

        public void WindowsTaskBarVisible(bool isVisible)
        {
            //try
            //{
                int hWnd = FindWindow("Shell_traywnd", "");
                if (isVisible)
                {
                    ShowWindow(hWnd, TASKBAR_SHOW);
                }
                else
                {
                    ShowWindow(hWnd, TASKBAR_HIDE);
                }
            //}
            //catch (Win32Exception ex)  {  }

        }

    }
}
