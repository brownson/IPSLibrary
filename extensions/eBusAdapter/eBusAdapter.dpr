library eBusAdapter;

uses
  Windows,
  StrUtils,
  Classes,
  SysUtils,
  UIPSTypes,
  UIPSModuleTypes,
  UeBusAdapter in 'UeBusAdapter.pas';

{$R *.res}

//------------------------------------------------------------------------------
procedure IPSModuleRegister(Kernel: IIPSKernel; ModuleRegistry: IIPSModuleRegistry); stdcall; forward;
procedure IPSModuleUnregister(); stdcall; forward;

//------------------------------------------------------------------------------
const LibInfo: TIPSLibraryInfo = (
                                 mUniqueID    : '{F4B3B98B-D4CE-4BBC-A774-84CA744147F8}';
                                 //--------------------------
                                 mAuthor      : 'IP-Symcon - CSS';
                                 mURL         : 'www.ip-symcon.de';
                                 mName        : 'Custom Library';
                                 mVersion     : {CompileVersion}$0200{/CompileVersion}; { Hi - MajorV, Lo - MinorV }
                                 mBuild       : {CompileBuild}0{/CompileBuild};
                                 mDate        : {CompileTime}0{/CompileTime};
                                 //--------------------------
                                 mKernelVersion : KERNEL_VERSION;
                                 //--------------------------
                                 fRegister    : IPSModuleRegister;
                                 fUnregister  : IPSModuleUnregister;
                               );

//------------------------------------------------------------------------------
var vKernel: IIPSKernel;

//------------------------------------------------------------------------------
procedure IPSLibraryInfo(var LibraryInfo: PIPSLibraryInfo); stdcall;
begin

 LibraryInfo := @LibInfo;

end;

//------------------------------------------------------------------------------
procedure IPSModuleRegister(Kernel: IIPSKernel; ModuleRegistry: IIPSModuleRegistry); stdcall;
begin

 vKernel := Kernel;
 vKernel.LogMessage(KL_MESSAGE, 0, LibInfo.mName, 'Register');

 //Register Classes
 ModuleRegistry.RegisterModule(TIPSeBusAdapter, TypeInfo(IIPSTestModule), 'TM');

end;

//------------------------------------------------------------------------------
procedure IPSModuleUnregister(); stdcall;
begin

 vKernel.LogMessage(KL_MESSAGE, 0, LibInfo.mName, 'Unregister');
 vKernel := NIL;

end;

//==============================================================================
exports IPSLibraryInfo;

begin
 //
end.

