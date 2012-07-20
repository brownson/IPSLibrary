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
using System.Drawing;
using System.Windows.Forms;
using System.Runtime.InteropServices;

namespace IPSToolLibrary
{

    public class MouseUtils
    {
        private int positionX;
        private int positionY;
        private int mouseIdleSince;
        private Timer timer;

        [DllImport("user32.dll")]
        private static extern Int32 ShowCursor(bool show);

        public MouseUtils()
        {
            positionX = 0;
            positionY = 0;
            mouseIdleSince = 0;

            timer = new Timer();
            timer.Interval = 1000;
            this.timer.Tick += new EventHandler(CalcMouseIdleSince);
            timer.Enabled = true;
        }


        public void CalcMouseIdleSince(object sender, EventArgs e)
        {
            Console.WriteLine("{0} Execute Timer, MouseIdleSince={1} ...",
                DateTime.Now.ToString("h:mm:ss.fff"), mouseIdleSince);

            mouseIdleSince++;
            if (positionX != Cursor.Position.X || positionY != Cursor.Position.Y)
            {
                mouseIdleSince = 0;
            }
            positionX = Cursor.Position.X;
            positionY = Cursor.Position.Y;
        }

        public static Point GetMousePosition()
        {
            return Cursor.Position;
        }

        public static void SetMousePosition(int x, int y)
        {
            Cursor.Position = new Point(x, y);
        }

        public int GetMouseIdleSince()
        {
            return mouseIdleSince;
        }

        public static void SetCursorVisible(bool isVisible)
        {
            if (isVisible)
            {
                ShowCursor(true);
            }
            else
            {
                ShowCursor(false);
            }

        }
    }
}
