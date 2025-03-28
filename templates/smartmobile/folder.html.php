<div id="folder" data-role="page">
    <?php echo $this->smartmobileHeader(array('backlink' => array('#backends', _("Backends")), 'logout' => true, 'title' => _("Backends"))) ?>

    <div data-role="content">
        <div id="gollem-folder-data" data-role="collapsible-set" data-content-theme="d"></div>
        <ul data-role="listview" id="gollem-files-data" data-inset="true" data-content-theme="d" data-filter="true"></ul>
    </div>
</div>


