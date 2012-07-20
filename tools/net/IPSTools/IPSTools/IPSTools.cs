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
using System.Windows.Forms;
using IPSToolLibrary;
using IPSTool.Properties;
using System.Net.Sockets;
using System.Threading;
using System.Net;
using System.Text;

using System.Diagnostics;
using System.Management;
using System.IO;

namespace BrownsonTool
{
    public partial class IPSTools : Form
    {
        private TcpServer tcpServer;

        public IPSTools()
        {
            InitializeComponent();
            tcpServer = new TcpServer((int)tcpPort.Value, this.Handle);
            tcpServer.AutoSendInterval = (int)autoSendInterval.Value;
            tcpServer.AutoSendIdle = autoSendIdle.Checked;
            if (checkBox_AutoStart.Checked)
            {
                tcpServer.Start();
            }
            pictureBoxServer_Refresh();
        }

        private void BrownsonTool_Resize(object sender, EventArgs e)
        {
            if (FormWindowState.Minimized == this.WindowState)
            {
                notifyIcon.Visible = true;
                notifyIcon.ShowBalloonTip(500);
                this.Hide();
            }
            else if (FormWindowState.Normal == this.WindowState)
            {
                notifyIcon.Visible = false;
            }
        }

        private void notifyIcon1_MouseDoubleClick(object sender, MouseEventArgs e)
        {
            this.Show();
        }

        private void showToolStripMenuItem_Click(object sender, EventArgs e)
        {
            this.Show();
        }

        private void BrownsonTool_FormClosing(object sender, FormClosingEventArgs e)
        {
            Settings.Default.Save();
            tcpServer.Stop();
            tcpServer = null;
        }

        private void startTcpServer_Click(object sender, EventArgs e)
        {

            tcpServer.Start();
            pictureBoxServer_Refresh();
        }

        private void stopTcpServer_Click(object sender, EventArgs e)
        {
            tcpServer.Stop();
            pictureBoxServer_Refresh();
        }

        private void storeSettings_Click(object sender, EventArgs e)
        {
            Settings.Default.Save();
        }

        private void tcpPort_ValueChanged(object sender, EventArgs e)
        {
            tcpServer.SetPort((int)tcpPort.Value);
            pictureBoxServer_Refresh();
        }

        private void pictureBoxServer_Refresh()
        {
            if (tcpServer.IsRunning())
            {
                pictureBoxServer.Image = IPSTool.Properties.Resources._64px_Green_pog_svg;
            }
            else
            {
                pictureBoxServer.Image = IPSTool.Properties.Resources._64px_Red_pog_svg;
            }
        }

        private void autoSendInterval_ValueChanged(object sender, EventArgs e)
        {
            tcpServer.AutoSendInterval = (int)autoSendInterval.Value;
        }

        private void autoSendIdle_CheckedChanged(object sender, EventArgs e)
        {
            tcpServer.AutoSendIdle = autoSendIdle.Checked;
        }

     }
}
