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
using System.Diagnostics;

namespace IPSToolLibrary
{
    class PerformanceUtils
    {
        PerformanceCounter cpuCounter;
        PerformanceCounter ramCounter;
        PerformanceCounter systemUpTime;

        public PerformanceUtils()
        {
            cpuCounter = new PerformanceCounter();
            cpuCounter.CategoryName = "Processor";
            cpuCounter.CounterName = "% Processor Time";
            cpuCounter.InstanceName = "_Total";

            ramCounter = new PerformanceCounter("Memory", "Available MBytes");

            systemUpTime = new PerformanceCounter("System", "System Up Time");
        }

        public string GetUsedCPU()
        {
            return cpuCounter.NextValue().ToString() + "%";
        }

        public string GetAvailableMemory()
        {
            return ramCounter.NextValue().ToString() + " MB";
        }

        public string SystemUpTime()
        {
            return systemUpTime.NextValue().ToString(); ;
        }

        public string getProcessMemory(string program)
        {
            foreach (Process process in Process.GetProcessesByName(program))
            {
                return process.WorkingSet64.ToString();
            };
            return "";
        }
    }
}
