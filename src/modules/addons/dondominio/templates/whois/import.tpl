<h2>{$LANG.whois_import_title}</h2>

<form action='{$links.import}' method='post' enctype='multipart/form-data'>
    <input type='file' name='whoisservers' />

    <div class='btn-container'>
        <input id='saveChanges' type='submit' value='{$LANG.import_btn}' class='btn btn-primary' />
        <a class='btn btn-default' href='{$links.index}'>{$LANG.config_cancel}</a>
    </div>
</form>