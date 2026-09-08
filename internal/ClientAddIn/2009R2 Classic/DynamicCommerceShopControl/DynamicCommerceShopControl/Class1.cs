using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Runtime.InteropServices;
using System.Windows.Forms;
using System.Security.Permissions;

namespace DynamicCommerceShopControl
{
    [InterfaceType(ComInterfaceType.InterfaceIsDual)]
    [Guid("61D32359-8237-40B6-AC72-60B32C9064C5")]
    interface IDynamicCommerceShopControl
    {
        [DispId(1)]
        void LoadUrl(string Request);

        [DispId(2)]
        void setSize(int x, int y);

        [DispId(3)]
        void setTitle(string title);

        [DispId(4)]
        void setMaximizeable(bool maximizeable);
    }

    public static class Globals
    {
        public static int x { get; set; }
        public static int y { get; set; }
        public static string title { get; set; }
        public static bool Maximizeable { get; set; }
    }

    [ClassInterface(ClassInterfaceType.AutoDual)]
    [Guid("61D32359-8237-40B6-AC72-60B32C9064C5")]
    public class ShopControl : IDynamicCommerceShopControl
    {
        public ShopControl()
        {
        }

        public void LoadUrl(string Request)
        {
            Form1 tmp_oForm = new Form1();
            tmp_oForm.Text = Globals.title;
            tmp_oForm.Width = Globals.x;
            tmp_oForm.Height = Globals.y;
            tmp_oForm.MaximizeBox = Globals.Maximizeable;
            if (Globals.Maximizeable)
            {
                tmp_oForm.FormBorderStyle = FormBorderStyle.FixedSingle;
            }
            tmp_oForm.LoadUrl(Request);
            tmp_oForm.Show();
        }

        public void setSize(int x, int y)
        {
            Globals.x = x;
            Globals.y = y;
        }

        public void setTitle(string title)
        {
            Globals.title = title;
        }

        public void setMaximizeable(bool maximizeable)
        {
            Globals.Maximizeable = maximizeable;
        }
    }
}
