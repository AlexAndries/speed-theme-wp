<?php $settings = \SpeedTheme\Admin\Exporting\Settings::getStaticOptions();
$postTypes = \SpeedTheme\Admin\Exporting\Settings::getPostTypes();

?>
<style>
  .col-6 {
    width: 49%;
    min-width: 320px;
    float: left;
    margin: 0 .5%;
  }
  
  .submit {
    width: 100%;
    float: left;
  }

  .listing table {
    width: 100%;
  }
  
  .listing td,
  .listing th{
    display: inline-block;
  }
  
  .listing {
    background: white;
    padding: 20px;
    display: inline-block;
    min-width: 100%;
  }
  
  .listing tr {
    padding: 5px 10px;
    margin-bottom: 0;
  }
  
  .listing tr:nth-child(2n) {
    background: #e3e3e3;
  }
  
  .listing .header {
    border-bottom: 1px solid #333;
    margin-bottom: 6px;
  }
  
  .listing td input {
    max-width: 100%;
  }
  
  .visible-item {
    width: 10%;
    text-align: center;
    padding-left: 10px;
    padding-right: 10px;
  }
  
  .name-item {
    width: 80%;
    text-align: left;
    padding-left: 10px;
    padding-right: 10px;
  }
</style>
<div class="wrap">
  <h1>Export/Import Handler</h1>
  <br/>
  <form action="" method="post" class="listing">
    <?php if (!empty($postTypes)) { ?>
      <div class="col-6">
        <h3>Export</h3>
        <table>
          <thead class="header">
          <tr>
            <th class="name-item">Post Name</th>
            <th class="visible-item">Export</th>
            <th class="visible-item">Import</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($postTypes as $postType) {
            $options = \SpeedTheme\Admin\Exporting\Settings::getOptions($postType->name); ?>
            <tr class="items">
              <td class="name-item"><?php echo $postType->label ?></td>
              <td class="visible-item">
                <input type="checkbox" name="export[]"
                       value="<?php echo $postType->name ?>" <?php echo $options && @$options['export'] ? 'checked' : ''
                ?>></td>
              <td class="visible-item">
                <input type="checkbox" name="import[]"
                       value="<?php echo $postType->name ?>" <?php echo $options && @$options['import'] ? 'checked' : ''
                ?>></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
    <?php } ?>
    <p class="submit"><input type="submit" class="button-primary" name="export-import-handler" value="Save Changes"></p>
  </form>
</div>
