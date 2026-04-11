/**
 * Provides the javascript for the clipboard.php script.
 *
 * See the enclosed file LICENSE for license information (GPL). If you
 * did not receive this file, see http://www.horde.org/licenses/gpl.
 */

var GollemClipboard = {

    // Variables set by clipboard.php:
    //   selectall, selectnone

    clickHandler: function(e)
    {
        if (e.button === 2) {
            return;
        }

        var elt = e.target.closest('#gollem-selectall, #gollem-pastebutton, #gollem-clearbutton, #gollem-cancelbutton');

        if (!elt) {
            return;
        }

        var form = document.getElementById('gollem-clipboard');

        switch (elt.id) {
        case 'gollem-selectall':
            var checked = elt.checked;
            var span = elt.nextElementSibling;
            if (span && span.tagName === 'SPAN') {
                span.textContent = checked ? this.selectnone : this.selectall;
            }
            Array.from(form.querySelectorAll('input[type="checkbox"]')).forEach(function(cb) {
                if (cb !== elt) {
                    cb.checked = checked;
                }
            });
            return;

        case 'gollem-pastebutton':
            document.getElementById('actionID').value = 'paste_items';
            form.submit();
            return;

        case 'gollem-clearbutton':
            document.getElementById('actionID').value = 'clear_items';
            form.submit();
            return;

        case 'gollem-cancelbutton':
            form.submit();
            return;
        }
    }

};

document.addEventListener('click', GollemClipboard.clickHandler.bind(GollemClipboard));
