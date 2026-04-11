/**
 * Provides the javascript for the selectlist.php script.
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 */

var Gollem_Selectlist = {

    returnID: function()
    {
        var formid = document.getElementById('formid').value,
            field = parent.opener.document[formid].selectlist_selectid,
            field2 = parent.opener.document[formid].actionID;

        if (parent.opener.closed || !field || !field2) {
            alert(GollemText.opener_window);
            window.close();
            return;
        }

        field.value = document.getElementById('cacheid').value;
        field2.value = 'selectlist_process';

        parent.opener.document[formid].submit();
        window.close();
    },

    clickHandler: function(e)
    {
        if (e.button === 2) {
            return;
        }

        var elt = e.target.closest('#addbutton, #cancelbutton, #donebutton');

        if (elt) {
            switch (elt.id) {
            case 'addbutton':
                document.getElementById('actionID').value = 'select';
                document.getElementById('selectlist').submit();
                return;

            case 'cancelbutton':
                window.close();
                return;

            case 'donebutton':
                this.returnID();
                return;
            }
        }
    },

    onDomLoad: function()
    {
        document.getElementById('selectlist').addEventListener('click', this.clickHandler.bind(this));
    }

};

document.addEventListener('DOMContentLoaded', Gollem_Selectlist.onDomLoad.bind(Gollem_Selectlist));
