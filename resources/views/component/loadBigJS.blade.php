<script>
const jsScriptUrl = '{{ $jsUrl }}?{{ hash_file('sha1', $jsPath) }}';
const progressEl = document.querySelector('#progress')
/** プログレスが変化する度に画面に反映させる関数 */
const updateProgressEvent = function(event){
    if(event.total){
        progressEl.style.width = `${(event.loaded / event.total) * 100}%`;
    }
}
const request = new XMLHttpRequest();
request.onprogress=updateProgressEvent;
request.open('GET', jsScriptUrl, true);
request.onreadystatechange = function () {
    if (request.readyState == 4) {
        // script タグとして HTML に追加
        const scriptEl = document.createElement('script');
        scriptEl.textContent = request.response;
        document.body.appendChild(scriptEl);

        progressEl && (progressEl.value = 100);

        progressEl && progressEl.remove()
    }
};
request.send();
</script>
