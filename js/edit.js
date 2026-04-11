/**
 * Provides the javascript for the edit.php script.
 *
 * See the enclosed file LICENSE for license information (GPL). If you
 * did not receive this file, see http://www.horde.org/licenses/gpl.
 */

var GollemEdit = {

    onDomLoad: function()
    {
        document.getElementById('cancelbutton').addEventListener('click', function() {
            window.close();
        });

        document.getElementById('gollem-edit').focus();
    }

};

document.addEventListener('DOMContentLoaded', GollemEdit.onDomLoad.bind(GollemEdit));
