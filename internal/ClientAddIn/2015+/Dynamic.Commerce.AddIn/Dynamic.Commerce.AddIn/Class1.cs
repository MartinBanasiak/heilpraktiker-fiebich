using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using Microsoft.Dynamics.Framework.UI.Extensibility;
using System.Runtime.Serialization;

namespace DynamicCommerce.DynamicCommerce.AddIn
{
    public delegate void SaveCompletedEventHandler(string status, string data);
    public delegate void UnsavedChangesEventHandler(bool saved);
    public delegate void ChangeActionButtonEventHandler(string actionButton, bool enabled);
    public delegate void ReceiveDataEventHandler(string status, string data);
    [ControlAddInExport("DynamicCommerce.DynamicCommerce.AddIn")]
    public interface IDynamicCommerceDynamicCommerceAddIn
    {
        [ApplicationVisible]
        event ApplicationEventHandler ControlAddInReady;
        [ApplicationVisible]
        event ApplicationEventHandler URLLoaded;
        [ApplicationVisible]
        event SaveCompletedEventHandler SaveCompleted;
        [ApplicationVisible]
        void LoadUrl(System.Uri url);
        [ApplicationVisible]
        void RequestSave();
        [ApplicationVisible]
        void RequestClose();
        [ApplicationVisible]
        event UnsavedChangesEventHandler UnsavedChanges;
        [ApplicationVisible]
        event ApplicationEventHandler ChangeDetected;
        [ApplicationVisible]
        void ExecuteAction(string action, string data);
        [ApplicationVisible]
        event ChangeActionButtonEventHandler ChangeActionButton;
        [ApplicationVisible]
        event ReceiveDataEventHandler ReceiveData;
    }
}
