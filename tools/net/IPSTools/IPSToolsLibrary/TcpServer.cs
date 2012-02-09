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
using System.Net.Sockets;
using System.Threading;
using System.Net;
using System.Drawing;
using System.Windows.Forms;
using System.Diagnostics;

namespace IPSToolLibrary
{

    public class TcpServer
    {
        private TcpListener tcpListener;
        private Thread listenThread;
        private List<TcpClient> clients;

        private MonitorUtils monitorUtils;
        private MouseUtils mouseUtils;
        private PerformanceUtils performanceUtils;

        private int tcpPort;
        private IntPtr guiHandle;
        private NotifyIcon notifyIcon;
        private System.Windows.Forms.Timer autoSendTimer;
        private int autoSendInterval;

        public bool AutoSendIdle { get; set; }
        public int AutoSendInterval
        {
            get { return this.autoSendInterval*1000; }
            set { this.autoSendInterval = value; if (autoSendTimer != null) autoSendTimer.Interval = AutoSendInterval; }
        }

        public TcpServer(int port, IntPtr guiHandle, NotifyIcon notifyIcon)
        {
            this.tcpPort = port;
            this.guiHandle = guiHandle;
            this.notifyIcon = notifyIcon;
            this.clients = new List<TcpClient>();
            this.mouseUtils = new MouseUtils();
            this.monitorUtils = new MonitorUtils(guiHandle);
            this.performanceUtils = new PerformanceUtils();

            this.AutoSendInterval = 10;
            autoSendTimer = new System.Windows.Forms.Timer();
            autoSendTimer.Interval = AutoSendInterval;
            this.autoSendTimer.Tick += new EventHandler(SendTimedMessages);
            autoSendTimer.Enabled = true;
        }

        public int GetPort()
        {
            return tcpPort;
        }

        public void SetPort(int port)
        {
            Stop();
            tcpPort = port;
        }

        public void Start()
        {
            if (tcpPort != 0 && !IsRunning())
            {
                this.tcpListener = new TcpListener(IPAddress.Any, tcpPort);
                this.listenThread = new Thread(new ThreadStart(ListenForClients));
                this.listenThread.Start();
            }
        }

        public void Stop()
        {
            if (listenThread != null)
            {
                listenThread.Abort();
                listenThread = null;
            }
            if (tcpListener != null)
            {
                tcpListener.Stop();
                tcpListener = null;
            }
            foreach (TcpClient client in clients)
            {
                client.Close();
            }
        }

        public bool IsRunning()
        {
            return listenThread != null;
        }


        private void ListenForClients()
        {
            this.tcpListener.Start();

            while (true)
            {
                //blocks until a client has connected to the server
                TcpClient client = this.tcpListener.AcceptTcpClient();
                clients.Add(client);
                Thread clientThread = new Thread(new ParameterizedThreadStart(HandleClientComm));
                clientThread.Start(client);
            }
        }
        private void SendTimedMessages(object sender, EventArgs e)
        {
            foreach (TcpClient client in clients)
            {
                if (AutoSendIdle)
                {
                    WriteToStream(client, string.Format("MouseIdleSince;{0}", mouseUtils.GetMouseIdleSince()));
                }
            }
        }

        private void WriteToStream(TcpClient client, string message)
        {
            if (message == "") return;


            // lock (this) {
            lock (client)
            {
                {
                    NetworkStream outputStream = client.GetStream();
                    byte[] outputMessage = new byte[4096];
                    ASCIIEncoding encoder = new ASCIIEncoding();
                    outputMessage = encoder.GetBytes(message);
                    outputStream.Write(outputMessage, 0, outputMessage.Length);
                    outputStream.Flush();
                }
            }
        }



        private void HandleClientComm(object client)
        {
            TcpClient tcpClient = (TcpClient)client;
            NetworkStream clientStream = tcpClient.GetStream();
            byte[] inputMessage = new byte[4096];
            int bytesRead;

            while (true)
            {
                bytesRead = 0;
                try
                {
                    //blocks until a client sends a message
                    bytesRead = clientStream.Read(inputMessage, 0, 4096);
                }
                catch
                {
                    break;
                }

                if (bytesRead == 0)
                {
                    //the client has disconnected from the server
                    break;
                }

                //message has successfully been received
                ASCIIEncoding encoder = new ASCIIEncoding();
                string inputString = encoder.GetString(inputMessage, 0, bytesRead);
                System.Diagnostics.Debug.WriteLine(inputString);

                string[] inputParams = inputString.Split(';');
                string outputString = inputParams[0];

                switch (inputParams[0])
                {
                    case "GetMousePosition":
                        outputString = string.Format("MousePosition;{0};{1}", MouseUtils.GetMousePosition().X.ToString(), MouseUtils.GetMousePosition().Y.ToString());
                        break;
                    case "GetMouseIdleSince":
                        outputString = string.Format("MouseIdleSince;{0}", mouseUtils.GetMouseIdleSince());
                        break;
                    case "CursorShow":
                        MouseUtils.SetCursorVisible(true);
                        break;
                    case "CursorHide":
                        MouseUtils.SetCursorVisible(false);
                        break;
                    case "StartScreenSaver":
                        monitorUtils.StartScreenSaver();
                        break;
                    case "ScreenPowerOff":
                        monitorUtils.ScreenPowerOff();
                        break;
                    case "ScreenPowerOn":
                        monitorUtils.ScreenPowerOn();
                        break;
                    case "FreeDiscSpace":
                        outputString = string.Format("{0};{1};{2}", inputParams[0], inputParams[1], DiscUtils.GetFreeDiscSpace(inputParams[1]));
                        break;
                    case "TotalDiscSpace":
                        outputString = string.Format("{0};{1};{2}", inputParams[0], inputParams[1], DiscUtils.GetTotalDiscSpace(inputParams[1]));
                        break;
                    case "DriveType":
                        outputString = string.Format("{0};{1};{2}", inputParams[0], inputParams[1], DiscUtils.GetDriveType(inputParams[1]));
                        break;
                    case "DriveFormat":
                        outputString = string.Format("{0};{1};{2}", inputParams[0], inputParams[1], DiscUtils.GetDriveFormat(inputParams[1]));
                        break;
                    case "IsDriveAvailable":
                        outputString = string.Format("{0};{1};{2}", inputParams[0], inputParams[1], DiscUtils.IsDriveAvailable(inputParams[1]));
                        break;
                    case "UsedCPU":
                        outputString = string.Format("{0};{1}", inputParams[0], performanceUtils.GetUsedCPU());
                        break;
                    case "AvailableMemory":
                        outputString = string.Format("{0};{1}", inputParams[0], performanceUtils.GetAvailableMemory());
                        break;
                    case "SystemUpTime":
                        outputString = string.Format("{0};{1}", inputParams[0], performanceUtils.SystemUpTime());
                        break;
                    case "ProcessMemory":
                        outputString = string.Format("{0};{1};{2}", inputParams[0], inputParams[1], performanceUtils.getProcessMemory(inputParams[1]));
                        break;
                    case "RunProgram":
                        Process.Start(inputParams[1], inputParams[2]);
                        outputString = string.Format("{0};{1};{2}", inputParams[0], inputParams[1], inputParams[2]);
                        break;
                    case "NotifyInfo":
                        notifyIcon.ShowBalloonTip(int.Parse(inputParams[1]), inputParams[2], inputParams[3],ToolTipIcon.Info);
                        break;
                    case "TaskBarHide":
                        //monitorUtils.WindowsTaskBarVisible(false);
                        Taskbar.Hide();
                        break;
                    case "TaskBarShow":
                        //monitorUtils.WindowsTaskBarVisible(true);
                        Taskbar.Show();
                        break;
                    default:
                        outputString = "Unknown Command";
                        break;
                }
                WriteToStream(tcpClient, outputString);

            }

            clients.Remove(tcpClient);
            tcpClient.Close();
        }

    }
}
