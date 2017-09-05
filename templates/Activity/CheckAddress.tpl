<p><strong>Es wurde eine geschäftliche Adresse übermittelt. Bitte überprüfen.</strong></p>

<table>
  <tr>
    <td>{ts domain="de.systopia.revent"}Organisation Name (row 1){/ts}</td>
    <td>{$data.organisation_name_1}</td>
  </tr>
  <tr>
    <td>{ts domain="de.systopia.revent"}Organisation Name (row 2){/ts}</td>
    <td>{$data.organisation_name_2}</td>
  </tr>
  <tr>
    <td>{ts domain="de.systopia.revent"}Street Address{/ts}</td>
    <td>{$data.street_address}</td>
  </tr>
  <tr>
    <td>{ts domain="de.systopia.revent"}Supplemental Address{/ts}</td>
    <td>{$data.supplemental_address_1}</td>
  </tr>
  <tr>
    <td>{ts domain="de.systopia.revent"}Postal Code{/ts}</td>
    <td>{$data.postal_code}</td>
  </tr>
  <tr>
    <td>{ts domain="de.systopia.revent"}City{/ts}</td>
    <td>{$data.city}</td>
  </tr>
  <tr>
    <td>{ts domain="de.systopia.revent"}Country{/ts}</td>
    <td>{$data.country}</td>
  </tr>
</table>

<p>Die zugehörige Registrierung befindet sich <a href="{crmURL p="civicrm/contact/view/participant" q="reset=1&id=`$participant_id`&cid=`$contact_id`&action=view"}">HIER<a>.</p>