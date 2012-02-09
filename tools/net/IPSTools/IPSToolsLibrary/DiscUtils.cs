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
using System.IO;

namespace IPSToolLibrary
{
    class DiscUtils
    {
        public static string GetFreeDiscSpace(string Drive)
        {
            try
            {
                return new DriveInfo(Drive).AvailableFreeSpace.ToString();
            }
            catch
            {
                return "";
            }
        }

        public static string GetTotalDiscSpace(string Drive)
        {
            try
            {
                return new DriveInfo(Drive).TotalSize.ToString();
            }
            catch
            {
                return "";
            }
        }

        public static string GetDriveFormat(string Drive)
        {
            try
            {
                return new DriveInfo(Drive).DriveFormat;
            }
            catch
            {
                return "";
            }
        }

        public static string GetDriveType(string Drive)
        {
            try
            {
                return new DriveInfo(Drive).DriveType.ToString();
            }
            catch
            {
                return "";
            }
        }

        public static string IsDriveAvailable(string Drive)
        {
            try
            {
                return new DriveInfo(Drive).IsReady.ToString();
            }
            catch
            {
                return "false";
            }
        }

    }
}
