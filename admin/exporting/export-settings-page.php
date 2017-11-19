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
  <h1>Export</h1>
  <br/>
  <div class="description-block">
    <strong>Unsupported ACF Types:</strong>
    <ul>
      <li>oEmbed</li>
      <li>Group</li>
      <li>Flexible Content</li>
      <li>Clone</li>
    </ul>
  </div>
  <form action="" method="post" class="listing">
    <p class="submit"><input type="submit" class="button-primary" name="export-<?php echo $_GET['post_type'] ?>-submit"
                             value="Download Export File"></p>
  </form>
</div>
