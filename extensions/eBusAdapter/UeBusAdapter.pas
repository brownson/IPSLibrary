unit UeBusAdapter;

// -------------------------------------------------------------------------------------------------------------
//
// IP-Symcom Extension für die Ankopplung eines eBus-Adapters über die serielle Schnittstelle
//
// @author: Andreas Brauneis
// @version
//   Version 2.50.1, 31.01.2012 
//
// -------------------------------------------------------------------------------------------------------------

interface

uses Windows, Messages, SysUtils, ActiveX, Classes, Forms, StdCtrls,
     Dialogs, StrUtils, ExtCtrls, ScktComp, WinSock,
     UIPSTypes, UIPSModuleTypes, UIPSDataTypes;

type
 IIPSTestModule = interface(IInvokable) ['{73EDE79A-4C87-497B-96B5-3DE3B202753A}']
 end;

 TIPSeBusAdapter = class(TIPSModuleObject,
                        IIPSModule,
                        IIPSTestModule,
                        IIPSReceiveString)
  private
   //--- Basic Structures
   //--- Custom Objects
   //--- Private Procedures/Functions
   msg:string;
  public
   constructor Create(IKernel: IIPSKernel; InstanceID: TInstanceID); override;
   destructor  Destroy; override;
   //--- IIPSModule implementation
   procedure LoadSettings(); override;
   procedure SaveSettings(); override;
   procedure ResetChanges(); override;
   procedure ApplyChanges(); override;
   { Data Points }
   procedure ReceiveText(Text: String); stdcall;
   { Class Functions }
   class function GetModuleID(): TStrGUID; override;
   class function GetModuleType(): TIPSModuleType; override;
   class function GetModuleName(): String; override;
   class function GetParentRequirements(): TStrGUIDs; override;
   class function GetImplemented(): TStrGUIDs; override;
   class function GetVendor(): String; override;
 end;

implementation

//------------------------------------------------------------------------------
class function TIPSeBusAdapter.GetVendor(): String;
begin
 Result := 'Brownson';
end;

//------------------------------------------------------------------------------
class function TIPSeBusAdapter.GetModuleID(): TStrGUID;
begin
 Result := GUIDToString(IIPSTestModule); //Will return Interface GUID
end;

//------------------------------------------------------------------------------
class function TIPSeBusAdapter.GetModuleType(): TIPSModuleType;
begin
 Result := mtDevice;
end;

//------------------------------------------------------------------------------
class function TIPSeBusAdapter.GetModuleName(): String;
begin
 Result := 'eBusAdapter';
end;

//------------------------------------------------------------------------------
class function TIPSeBusAdapter.GetParentRequirements(): TStrGUIDs;
begin

 SetLength(Result, 1);
 Result[0] := GUIDToString(IIPSSendString);

end;

//------------------------------------------------------------------------------
class function TIPSeBusAdapter.GetImplemented(): TStrGUIDs;
begin
 SetLength(Result, 1);
 Result[0] := GUIDToString(IIPSReceiveString);
end;


//------------------------------------------------------------------------------
constructor TIPSeBusAdapter.Create(IKernel: IIPSKernel; InstanceID: TInstanceID);
begin

 inherited;

 //Register Variables
 RegisterVariable('Data','Data',vtString,'~String');
 msg:='';

 //Load/Apply Settings
 ConstructorPostProcess;

 //Check Parent
 RequireParent(IIPSClientSocket, True);

end;

//------------------------------------------------------------------------------
destructor  TIPSeBusAdapter.Destroy;
begin

 //Save Settings
 DestructorPreProcess;

 inherited;

end;

//------------------------------------------------------------------------------
procedure TIPSeBusAdapter.LoadSettings();
begin
 inherited;
end;

//------------------------------------------------------------------------------
procedure TIPSeBusAdapter.SaveSettings();
begin
 inherited;
end;

//------------------------------------------------------------------------------
procedure TIPSeBusAdapter.ResetChanges();
begin
 inherited;
end;

//------------------------------------------------------------------------------
procedure TIPSeBusAdapter.ApplyChanges();
begin
 inherited;
end;

//------------------------------------------------------------------------------
procedure TIPSeBusAdapter.ReceiveText(Text:string); stdcall;
var
  i: Integer;
  msgHexChar:string;
begin
  If Length(Text)>0 Then
     for i := 1 to Length(Text) do
     begin
        msgHexChar := IntToHex(Ord(Text[i]),2);
        if msgHexChar = 'AA' then
        begin
		       if msg <> '' then
		       begin
              fKernel.VariableManager.WriteVariableString(GetStatusVariableID('Data'), msg);
              msg := '';
		       end;
		    end else begin
		       msg := msg + msgHexChar;
		    end;
	   end;
end;

end.
