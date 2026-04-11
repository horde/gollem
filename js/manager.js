/**
 * Provides the javascript for the manager.php script.
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 */

var Gollem = {

    getChecked: function()
    {
        return this.getElements().filter(function(e) {
            return e.checked;
        });
    },

    getElements: function()
    {
        return Array.from(document.getElementById('manager').querySelectorAll('input[name="items[]"]'));
    },

    getSelected: function()
    {
        return this.getChecked().map(function(e) {
            return e.value;
        }).join("\n");
    },

    toggleSelection: function()
    {
        var e = this.getElements(),
            checked = (this.getChecked().length != e.length);
        e.forEach(function(f) {
            f.checked = checked;
        });
    },

    getItemsArray: function()
    {
        var i = 0,
            it = Array.from(document.getElementById('manager').querySelectorAll('input[name="itemTypes[]"]'));

        return this.getElements().map(function(m) {
            return { c: m.checked, v: m.value, t: it[i++].value };
        });
    },

    getSelectedFoldersList: function()
    {
        return this.getItemsArray().map(function(i) {
            return (i.c && i.t == '**dir') ? i.v : null;
        }).filter(function(x) { return x != null; }).join("\n");
    },

    _clearChecks: function()
    {
        this.getChecked().forEach(function(e) {
            e.checked = false;
        });
    },

    renameItems: function()
    {
        var c = this.getChecked();
        if (c.length) {
            c[0].checked = false;
            document.getElementById('renamefrm_oldname').value = c[0].value;
            HordeDialog.display({
                form_id: 'renamefrm',
                input_val: c[0].value,
                text: GollemText.rename
            });
        }
    },

    deleteItems: function()
    {
        var cont = true, sf;

        if (window.confirm(GollemText.delete_confirm_1 + '\n' + this.getSelected() + '\n' + GollemText.delete_confirm_2)) {
            if (GollemVar.warn_recursive) {
                sf = this.getSelectedFoldersList();
                if (sf !== '' &&
                    !window.confirm(GollemText.delete_recurs_1 + '\n' + sf + '\n' + GollemText.delete_recurs_2)) {
                    cont = false;
                }
            }
        } else {
            cont = false;
        }

        if (cont) {
            document.getElementById('actionID').value = 'delete_items';
            document.getElementById('manager').submit();
        }
    },

    createFolderOK: function()
    {
        var val = document.getElementById('dialog_input').value;
        if (val) {
            document.getElementById('new_folder').value = val;
            document.getElementById('actionID').value = 'create_folder';
            document.getElementById('manager').submit();
        }
    },

    chmodOK: function()
    {
        var all = 0, group = 0, owner = 0;

        Array.from(document.getElementById('chmodfrm').elements).forEach(function(e) {
            if (e.name == 'owner[]' && e.checked) {
                owner |= e.value;
            } else if (e.name == 'group[]' && e.checked) {
                group |= e.value;
            } else if (e.name == 'all[]' && e.checked) {
                all |= e.value;
            }
        });

        document.getElementById('chmod').value = '0' + owner + '' + group + '' + all;
        document.getElementById('actionID').value = 'chmod_modify';
        document.getElementById('manager').submit();
    },

    renameOK: function()
    {
        var c = this.getChecked(),
            newname = document.getElementById('dialog_input').value,
            newNames = document.getElementById('new_names').value,
            oldname = document.getElementById('renamefrm_oldname').value,
            oldNames = document.getElementById('old_names').value;

        if (newname && newname != oldname) {
            newNames += '|' + newname;
            oldNames += '|' + oldname;
        }

        if (newNames.startsWith('|')) {
            newNames = newNames.substring(1);
        }
        if (oldNames.startsWith('|')) {
            oldNames = oldNames.substring(1);
        }

        document.getElementById('new_names').value = newNames;
        document.getElementById('old_names').value = oldNames;

        if (c.length) {
            setTimeout(Gollem.renameItems.bind(Gollem), 0);
        } else {
            document.getElementById('actionID').value = 'rename_items';
            document.getElementById('manager').submit();
        }
    },

    changeDirectoryOK: function()
    {
        var val = document.getElementById('dialog_input').value;
        if (val) {
            document.getElementById('dir').value = val;
            document.getElementById('manager').submit();
        }
    },

    uploadFields: function()
    {
        return Array.from(document.getElementById('manager').querySelectorAll('input[type="file"]')).filter(function(m) {
            return m.name.substr(0, 12) == 'file_upload_';
        });
    },

    uploadFile: function()
    {
        if (this.uploadsExist()) {
            document.getElementById('actionID').value = 'upload_file';
            document.getElementById('manager').submit();
        }
    },

    uploadsExist: function()
    {
        if (GollemVar.empty_input ||
            this.uploadFields().find(function(f) { return f.value; })) {
            return true;
        }
        alert(GollemText.specify_upload);
        document.getElementById('file_upload_1').focus();
        return false;
    },

    uploadChanged: function()
    {
        if (GollemVar.empty_input) {
            return;
        }

        var file, lastRow,
            fields = this.uploadFields(),
            usedFields = fields.filter(function(f) { return f.value.length; }).length;

        if (usedFields == fields.length) {
            lastRow = document.getElementById('upload_row_' + usedFields);
            if (lastRow) {
                file = document.createElement('input');
                file.type = 'file';
                file.name = 'file_upload_' + (usedFields + 1);
                file.size = 25;

                var strong = document.createElement('strong');
                strong.textContent = GollemText.file + ' ' + (usedFields + 1) + ':';

                var div = document.createElement('div');
                div.id = 'upload_row_' + (usedFields + 1);
                div.appendChild(strong);
                div.appendChild(document.createTextNode(' '));
                div.appendChild(file);

                lastRow.after(div);
                file.addEventListener('change', this.uploadChanged.bind(this));
            }
        }
    },

    clickHandler: function(e)
    {
        if (e.button === 2) {
            return;
        }

        var elt = e.target.closest('[id]');

        while (elt) {
            switch (elt.id) {
            case 'gollem-changefolder':
                this._clearChecks();
                HordeDialog.display({
                    form_id: 'cdfrm',
                    text: GollemText.change_directory
                });
                e.preventDefault();
                return;

            case 'checkall':
                this.toggleSelection();
                break;

            case 'gollem-createfolder':
                this._clearChecks();
                HordeDialog.display({
                    form_id: 'createfrm',
                    text: GollemText.create_folder
                });
                e.preventDefault();
                return;

            case 'uploadfile':
                this.uploadFile();
                break;

            case 'gollem-rename':
                if (!this.getChecked().length) {
                    alert(GollemText.select_item);
                    break;
                }
                this.renameItems();
                break;

            case 'gollem-delete':
                if (!this.getChecked().length) {
                    alert(GollemText.select_item);
                    break;
                }
                this.deleteItems();
                break;

            case 'gollem-chmod':
                if (!this.getChecked().length) {
                    alert(GollemText.select_item);
                    break;
                }
                var attrs = document.getElementById('gollem-attributes');
                var cloned = attrs.cloneNode(true);
                cloned.hidden = false;
                cloned.style.display = '';
                HordeDialog.display({
                    form: cloned,
                    form_id: 'chmodfrm',
                    form_opts: { action: GollemVar.actionUrl },
                    header: GollemText.permissions
                });
                break;

            case 'gollem-cut':
                if (!this.getChecked().length) {
                    alert(GollemText.select_item);
                    break;
                }
                document.getElementById('actionID').value = 'cut_items';
                document.getElementById('manager').submit();
                break;

            case 'gollem-copy':
                if (!this.getChecked().length) {
                    alert(GollemText.select_item);
                    break;
                }
                document.getElementById('actionID').value = 'copy_items';
                document.getElementById('manager').submit();
                break;
            }

            elt = elt.parentElement ? elt.parentElement.closest('[id]') : null;
        }
    },

    okHandler: function(e)
    {
        var elt = e.target;
        if (!elt.id) {
            elt = elt.closest('[id]');
        }

        if (elt) {
            switch (elt.id) {
            case 'cdfrm':
                Gollem.changeDirectoryOK();
                break;

            case 'chmodfrm':
                Gollem.chmodOK();
                break;

            case 'createfrm':
                Gollem.createFolderOK();
                break;

            case 'renamefrm':
                Gollem.renameOK();
                break;
            }
        }
    },

    closeHandler: function()
    {
        document.getElementById('new_names').value = '';
        document.getElementById('old_names').value = '';
    },

    onDomLoad: function()
    {
        var tmp = document.getElementById('file_upload_1');
        if (tmp) {
            tmp.addEventListener('change', this.uploadChanged.bind(this));
        }
    }

};

document.addEventListener('DOMContentLoaded', Gollem.onDomLoad.bind(Gollem));
document.addEventListener('click', Gollem.clickHandler.bind(Gollem));
document.addEventListener('HordeDialog:onClick', Gollem.okHandler.bind(Gollem));
document.addEventListener('HordeDialog:close', Gollem.closeHandler.bind(Gollem));
