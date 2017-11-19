<style>
  .submit {
    width: 100%;
    float: left;
  }
  
  .listing {
    background: white;
    padding: 20px;
    display: inline-block;
    min-width: 100%;
  }


  .description-block {
    display: inline-block;
  }

  .description-block strong {
    vertical-align: top;
  }

  .description-block ul {
    display: inline-block;
    padding-left: 10px;
  }
</style>
<div class="wrap">
  <h1>Import</h1>
  <p>Please use the export to get the head table</p>
  <div class="description-block">
    <strong>Unsupported ACF Types:</strong>
    <ul>
      <li>oEmbed</li>
      <li>Page Link</li>
      <li>Group</li>
      <li>Flexible Content</li>
      <li>Clone</li>
    </ul>
  </div>
  <br/>
  <form action="" method="post" class="listing" enctype="multipart/form-data">
    <p><input type="file" name="file"></p>
    <p class="submit"><input type="submit" class="button-primary" name="import-<?php echo $_GET['post_type'] ?>-submit"
                             value="Import File"></p>
  </form>
</div>
