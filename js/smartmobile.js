/**
 * jQuery Mobile UI Gollem application logic.
 *
 * @author   Heinz Schweiger <heinz@htl-steyr.ac.at>
 * @copyright  2014-2025 Horde LLC
 * @license    ASL (http://www.horde.org/licenses/apache)
 */

var GollemMobile = {

    /**
     * Event handler for the pagebeforechange event that implements loading of
     * deep-linked pages.
     *
     * @param object e     Event object.
     * @param object data  Event data.
     */
    toPage: function (e, data) {
        switch (data.options.parsedUrl.view) {
            case 'folder':
                GollemMobile.folder(data);
                e.preventDefault();
                break;
        }
    },

    /**
     * View folder of backend.
     *
     * @param object data  Page change data object.
     */
    folder: function (data) {
        var purl = data.options.parsedUrl;

        HordeMobile.changePage('folder', data);
        HordeMobile.doAction(
            'smartmobileFolderContent',
            {
                backend_key: purl.params.backend_key,
                dir: purl.params.dir
            },
            GollemMobile.folderLoaded
        );
    },

    /**
     * Callback method after folder has been loaded.
     *
     * @param object r  The Ajax response object.
     */
    folderLoaded: function (r) {
        if (r.error) {
            HordeMobile.changePage('folder');
            return;
        }

        var tmp,
            gfd = $('#gollem-folder-data').empty(),
            gfiles = $('#gollem-files-data').empty();

        $('#folder .smartmobile-title').text(r.backendName);

        if (r.filelist) {
            gfiles.append(
                $('<li></li>')
                    .attr('data-role', 'list-divider')
                    .attr('data-mini', true)
                    .attr('data-theme', 'b')
                    .append(
                        $('<a></a>').text(r.filelist.l)
                    )
            );

            $.each(r.filelist.e, function (k, v) {
                gfiles.append(
                    $('<li></li>')
                        .append(
                            $('<a></a>')
                                .append(
                                    $(v.i).addClass('ui-li-icon')
                                )
                                .attr('href', v.u)
                                .attr('rel', 'external')
                                .append(
                                    document.createTextNode(v.n)
                                )
                                .append(
                                    $('<span> </span>')
                                        .addClass('file-date')
                                        .text(' ' + v.d)
                                )
                        )
                );
            });
        }


        if (r.folderlist) {
            tmp = $('<ul></ul>')
                .attr('data-role', 'listview')
                .attr('data-inset', 'true')
                .attr('data-filter', 'true');

            $.each(r.folderlist.e, function (k, v) {
                tmp.append(
                    $('<li></li>').append(
                        $('<a></a>').attr('href', v.u).text(v.n)
                    )
                );
            });

            gfd.append(
                $('<div></div>')
                    .attr('data-role', 'collapsible')
                    .attr('data-collapsed', false)
                    .attr('data-mini', true)
                    .attr('data-theme', 'b')
                    .append(
                        $('<h3></h3>').text(r.folderlist.l)
                    ).append(
                    $('<div></div>').append(tmp)
                )
            );
        }

        gfiles.listview('refresh');
        gfd.collapsibleset('refresh')
            .find(':jqmData(role="listview")').listview();
        $('#entry :jqmData(role="content")').show();
    },

    /**
     * Event handler for the document-ready event, responsible for the initial
     * setup.
     */
    onDocumentReady: function () {
        $(document).bind('pagebeforechange', GollemMobile.toPage);
    }

};

// JQuery Mobile setup
$(GollemMobile.onDocumentReady);
