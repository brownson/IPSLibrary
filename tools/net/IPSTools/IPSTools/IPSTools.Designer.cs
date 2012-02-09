namespace BrownsonTool
{
  partial class IPSTools
  {
    /// <summary>
    /// Required designer variable.
    /// </summary>
    private System.ComponentModel.IContainer components = null;

    /// <summary>
    /// Clean up any resources being used.
    /// </summary>
    /// <param name="disposing">true if managed resources should be disposed; otherwise, false.</param>
    protected override void Dispose(bool disposing)
    {
      if (disposing && (components != null))
      {
        components.Dispose();
      }
      base.Dispose(disposing);
    }

    #region Windows Form Designer generated code

    /// <summary>
    /// Required method for Designer support - do not modify
    /// the contents of this method with the code editor.
    /// </summary>
    private void InitializeComponent()
    {
        this.components = new System.ComponentModel.Container();
        System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(IPSTools));
        this.notifyIcon = new System.Windows.Forms.NotifyIcon(this.components);
        this.contextMenuStrip1 = new System.Windows.Forms.ContextMenuStrip(this.components);
        this.showToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
        this.storeSettings = new System.Windows.Forms.Button();
        this.groupBox1 = new System.Windows.Forms.GroupBox();
        this.checkBox_AutoStart = new System.Windows.Forms.CheckBox();
        this.pictureBoxServer = new System.Windows.Forms.PictureBox();
        this.stopTcpServer = new System.Windows.Forms.Button();
        this.tcpPort = new System.Windows.Forms.NumericUpDown();
        this.label1 = new System.Windows.Forms.Label();
        this.startTcpServer = new System.Windows.Forms.Button();
        this.groupBox2 = new System.Windows.Forms.GroupBox();
        this.autoSendInterval = new System.Windows.Forms.NumericUpDown();
        this.label2 = new System.Windows.Forms.Label();
        this.autoSendIdle = new System.Windows.Forms.CheckBox();
        this.contextMenuStrip1.SuspendLayout();
        this.groupBox1.SuspendLayout();
        ((System.ComponentModel.ISupportInitialize)(this.pictureBoxServer)).BeginInit();
        ((System.ComponentModel.ISupportInitialize)(this.tcpPort)).BeginInit();
        this.groupBox2.SuspendLayout();
        ((System.ComponentModel.ISupportInitialize)(this.autoSendInterval)).BeginInit();
        this.SuspendLayout();
        // 
        // notifyIcon
        // 
        this.notifyIcon.BalloonTipIcon = System.Windows.Forms.ToolTipIcon.Info;
        this.notifyIcon.BalloonTipText = "Helper Tools for IPS";
        this.notifyIcon.BalloonTipTitle = "IP-Symcon Tools";
        this.notifyIcon.ContextMenuStrip = this.contextMenuStrip1;
        this.notifyIcon.Icon = ((System.Drawing.Icon)(resources.GetObject("notifyIcon.Icon")));
        this.notifyIcon.Text = "IP-Symcon Tools";
        this.notifyIcon.Visible = true;
        this.notifyIcon.MouseDoubleClick += new System.Windows.Forms.MouseEventHandler(this.notifyIcon1_MouseDoubleClick);
        // 
        // contextMenuStrip1
        // 
        this.contextMenuStrip1.Items.AddRange(new System.Windows.Forms.ToolStripItem[] {
            this.showToolStripMenuItem});
        this.contextMenuStrip1.Name = "contextMenuStrip1";
        this.contextMenuStrip1.Size = new System.Drawing.Size(104, 26);
        // 
        // showToolStripMenuItem
        // 
        this.showToolStripMenuItem.Name = "showToolStripMenuItem";
        this.showToolStripMenuItem.Size = new System.Drawing.Size(103, 22);
        this.showToolStripMenuItem.Text = "Show";
        this.showToolStripMenuItem.Click += new System.EventHandler(this.showToolStripMenuItem_Click);
        // 
        // storeSettings
        // 
        this.storeSettings.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
        this.storeSettings.Location = new System.Drawing.Point(415, 187);
        this.storeSettings.Name = "storeSettings";
        this.storeSettings.Size = new System.Drawing.Size(135, 23);
        this.storeSettings.TabIndex = 4;
        this.storeSettings.Text = "Einstellungen Speichern";
        this.storeSettings.UseVisualStyleBackColor = true;
        this.storeSettings.Click += new System.EventHandler(this.storeSettings_Click);
        // 
        // groupBox1
        // 
        this.groupBox1.Anchor = ((System.Windows.Forms.AnchorStyles)(((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Left)
                    | System.Windows.Forms.AnchorStyles.Right)));
        this.groupBox1.Controls.Add(this.checkBox_AutoStart);
        this.groupBox1.Controls.Add(this.pictureBoxServer);
        this.groupBox1.Controls.Add(this.stopTcpServer);
        this.groupBox1.Controls.Add(this.tcpPort);
        this.groupBox1.Controls.Add(this.label1);
        this.groupBox1.Controls.Add(this.startTcpServer);
        this.groupBox1.Location = new System.Drawing.Point(12, 12);
        this.groupBox1.Name = "groupBox1";
        this.groupBox1.Size = new System.Drawing.Size(535, 77);
        this.groupBox1.TabIndex = 12;
        this.groupBox1.TabStop = false;
        this.groupBox1.Text = "Server Einstellungen";
        // 
        // checkBox_AutoStart
        // 
        this.checkBox_AutoStart.AutoSize = true;
        this.checkBox_AutoStart.Checked = global::IPSTool.Properties.Settings.Default.AutoStartTcpServer;
        this.checkBox_AutoStart.DataBindings.Add(new System.Windows.Forms.Binding("Checked", global::IPSTool.Properties.Settings.Default, "AutoStartTcpServer", true, System.Windows.Forms.DataSourceUpdateMode.OnPropertyChanged));
        this.checkBox_AutoStart.Location = new System.Drawing.Point(9, 47);
        this.checkBox_AutoStart.Name = "checkBox_AutoStart";
        this.checkBox_AutoStart.Size = new System.Drawing.Size(154, 17);
        this.checkBox_AutoStart.TabIndex = 22;
        this.checkBox_AutoStart.Text = "Server automatisch Starten";
        this.checkBox_AutoStart.UseVisualStyleBackColor = true;
        // 
        // pictureBoxServer
        // 
        this.pictureBoxServer.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Right)));
        this.pictureBoxServer.Image = global::IPSTool.Properties.Resources._64px_Red_pog_svg;
        this.pictureBoxServer.InitialImage = ((System.Drawing.Image)(resources.GetObject("pictureBoxServer.InitialImage")));
        this.pictureBoxServer.Location = new System.Drawing.Point(244, 18);
        this.pictureBoxServer.Name = "pictureBoxServer";
        this.pictureBoxServer.Size = new System.Drawing.Size(25, 24);
        this.pictureBoxServer.SizeMode = System.Windows.Forms.PictureBoxSizeMode.StretchImage;
        this.pictureBoxServer.TabIndex = 21;
        this.pictureBoxServer.TabStop = false;
        // 
        // stopTcpServer
        // 
        this.stopTcpServer.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Right)));
        this.stopTcpServer.Location = new System.Drawing.Point(403, 19);
        this.stopTcpServer.Name = "stopTcpServer";
        this.stopTcpServer.Size = new System.Drawing.Size(122, 23);
        this.stopTcpServer.TabIndex = 20;
        this.stopTcpServer.Text = "Stop TCP Server";
        this.stopTcpServer.UseVisualStyleBackColor = true;
        this.stopTcpServer.Click += new System.EventHandler(this.stopTcpServer_Click);
        // 
        // tcpPort
        // 
        this.tcpPort.DataBindings.Add(new System.Windows.Forms.Binding("Value", global::IPSTool.Properties.Settings.Default, "tcpPort", true, System.Windows.Forms.DataSourceUpdateMode.OnPropertyChanged));
        this.tcpPort.Location = new System.Drawing.Point(41, 19);
        this.tcpPort.Maximum = new decimal(new int[] {
            100000,
            0,
            0,
            0});
        this.tcpPort.Name = "tcpPort";
        this.tcpPort.Size = new System.Drawing.Size(70, 20);
        this.tcpPort.TabIndex = 19;
        this.tcpPort.Value = global::IPSTool.Properties.Settings.Default.tcpPort;
        this.tcpPort.ValueChanged += new System.EventHandler(this.tcpPort_ValueChanged);
        // 
        // label1
        // 
        this.label1.AutoSize = true;
        this.label1.Location = new System.Drawing.Point(6, 21);
        this.label1.Name = "label1";
        this.label1.Size = new System.Drawing.Size(29, 13);
        this.label1.TabIndex = 17;
        this.label1.Text = "Port:";
        // 
        // startTcpServer
        // 
        this.startTcpServer.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Right)));
        this.startTcpServer.Location = new System.Drawing.Point(275, 19);
        this.startTcpServer.Name = "startTcpServer";
        this.startTcpServer.Size = new System.Drawing.Size(122, 23);
        this.startTcpServer.TabIndex = 18;
        this.startTcpServer.Text = "Start TCP Server";
        this.startTcpServer.UseVisualStyleBackColor = true;
        this.startTcpServer.Click += new System.EventHandler(this.startTcpServer_Click);
        // 
        // groupBox2
        // 
        this.groupBox2.Anchor = ((System.Windows.Forms.AnchorStyles)((((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Bottom)
                    | System.Windows.Forms.AnchorStyles.Left)
                    | System.Windows.Forms.AnchorStyles.Right)));
        this.groupBox2.Controls.Add(this.autoSendInterval);
        this.groupBox2.Controls.Add(this.label2);
        this.groupBox2.Controls.Add(this.autoSendIdle);
        this.groupBox2.Location = new System.Drawing.Point(13, 96);
        this.groupBox2.Name = "groupBox2";
        this.groupBox2.Size = new System.Drawing.Size(534, 82);
        this.groupBox2.TabIndex = 13;
        this.groupBox2.TabStop = false;
        this.groupBox2.Text = "Messages";
        // 
        // autoSendInterval
        // 
        this.autoSendInterval.DataBindings.Add(new System.Windows.Forms.Binding("Value", global::IPSTool.Properties.Settings.Default, "AutoSendInterval", true, System.Windows.Forms.DataSourceUpdateMode.OnPropertyChanged));
        this.autoSendInterval.Location = new System.Drawing.Point(222, 19);
        this.autoSendInterval.Maximum = new decimal(new int[] {
            100000,
            0,
            0,
            0});
        this.autoSendInterval.Name = "autoSendInterval";
        this.autoSendInterval.Size = new System.Drawing.Size(70, 20);
        this.autoSendInterval.TabIndex = 21;
        this.autoSendInterval.Value = global::IPSTool.Properties.Settings.Default.AutoSendInterval;
        this.autoSendInterval.ValueChanged += new System.EventHandler(this.autoSendInterval_ValueChanged);
        // 
        // label2
        // 
        this.label2.AutoSize = true;
        this.label2.Location = new System.Drawing.Point(6, 21);
        this.label2.Name = "label2";
        this.label2.Size = new System.Drawing.Size(212, 13);
        this.label2.TabIndex = 20;
        this.label2.Text = "Automatisches Sende Interval [Sekunden]: ";
        // 
        // autoSendIdle
        // 
        this.autoSendIdle.AutoSize = true;
        this.autoSendIdle.Checked = global::IPSTool.Properties.Settings.Default.AutoSendIdle;
        this.autoSendIdle.CheckState = System.Windows.Forms.CheckState.Checked;
        this.autoSendIdle.DataBindings.Add(new System.Windows.Forms.Binding("Checked", global::IPSTool.Properties.Settings.Default, "AutoSendIdle", true, System.Windows.Forms.DataSourceUpdateMode.OnPropertyChanged));
        this.autoSendIdle.Location = new System.Drawing.Point(9, 45);
        this.autoSendIdle.Name = "autoSendIdle";
        this.autoSendIdle.Size = new System.Drawing.Size(206, 17);
        this.autoSendIdle.TabIndex = 0;
        this.autoSendIdle.Text = "Automatische \"Mouse Idle\" Messages";
        this.autoSendIdle.UseVisualStyleBackColor = true;
        this.autoSendIdle.CheckedChanged += new System.EventHandler(this.autoSendIdle_CheckedChanged);
        // 
        // IPSTools
        // 
        this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
        this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
        this.ClientSize = new System.Drawing.Size(559, 218);
        this.Controls.Add(this.groupBox2);
        this.Controls.Add(this.groupBox1);
        this.Controls.Add(this.storeSettings);
        this.Name = "IPSTools";
        this.Text = "IP-Symcon Tools   (© Brownson)";
        this.FormClosing += new System.Windows.Forms.FormClosingEventHandler(this.BrownsonTool_FormClosing);
        this.Resize += new System.EventHandler(this.BrownsonTool_Resize);
        this.contextMenuStrip1.ResumeLayout(false);
        this.groupBox1.ResumeLayout(false);
        this.groupBox1.PerformLayout();
        ((System.ComponentModel.ISupportInitialize)(this.pictureBoxServer)).EndInit();
        ((System.ComponentModel.ISupportInitialize)(this.tcpPort)).EndInit();
        this.groupBox2.ResumeLayout(false);
        this.groupBox2.PerformLayout();
        ((System.ComponentModel.ISupportInitialize)(this.autoSendInterval)).EndInit();
        this.ResumeLayout(false);

    }

    #endregion

    private System.Windows.Forms.NotifyIcon notifyIcon;
    private System.Windows.Forms.ContextMenuStrip contextMenuStrip1;
    private System.Windows.Forms.ToolStripMenuItem showToolStripMenuItem;
    private System.Windows.Forms.Button storeSettings;
    private System.Windows.Forms.GroupBox groupBox1;
    private System.Windows.Forms.CheckBox checkBox_AutoStart;
    private System.Windows.Forms.PictureBox pictureBoxServer;
    private System.Windows.Forms.Button stopTcpServer;
    private System.Windows.Forms.NumericUpDown tcpPort;
    private System.Windows.Forms.Label label1;
    private System.Windows.Forms.Button startTcpServer;
    private System.Windows.Forms.GroupBox groupBox2;
    private System.Windows.Forms.CheckBox autoSendIdle;
    private System.Windows.Forms.NumericUpDown autoSendInterval;
    private System.Windows.Forms.Label label2;
  }
}

