<form method="POST" action="/test-optimizer" enctype="multipart/form-data">
    @csrf
    <input type="file" name="archivo" accept=".pdf,.jpg,.jpeg,.png">
    <button type="submit">Probar</button>
</form>