<style>
    .border1 {
        border: 1px solid gray;
    }
    
    .border2 {
        border-top: 1px solid gray;
        border-right: 1px solid gray;
        border-bottom: 1px solid gray;
    }
</style>
<table cellpadding="0" cellspacing="0" width="100%" border="0" height="420">
    <tr valign="top">
        <td width="25%" class="border1">
            <iframe id="comaker_list" class="master_comaker" width="100%" frameborder="0"></iframe>
        </td>
        <td width="75%" class="border2">
            <iframe id="comaker_form" width="100%" frameborder="0"></iframe>
        </td>
    </tr>
</table>
<script src="<?= base_url() ?>system/application/assets/js/jquery-2.1.3.min.js" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        $('#comaker_list').prop('src', 'comaker_list');
        $('#comaker_form').prop('src', 'coMaker_form');
        var listHeight = $('#comaker_list').contents().height();
        var listHeight = listHeight * 2.9;
        $('#comaker_list').css('height', listHeight + 'px');
        var formHeight = $('#comaker_form').contents().height();
        var formHeight = formHeight * 2.9;
        $('#comaker_form').css('height', formHeight + 'px');
    });
</script>