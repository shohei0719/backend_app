/*
 * 削除ボタン押下時のチェック
 */
function delete_check(){
    if(window.confirm('削除してもよろしいですか？')){//確認ダイアログを表示
        return true;
    } else {
        return false;
    }
}

/*
 * 画像ファイル選択時のサムネイル用
 */
$(function() {
	// jQuery Upload Thumbs 
	$('form input:file').uploadThumbs({
		position : 0,      // 0:before, 1:after, 2:parent.prepend, 3:parent.append,
						// any: arbitrarily jquery selector
		imgbreak : true    // append <br> after thumbnail images
	});
});