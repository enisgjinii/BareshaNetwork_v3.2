try {
  const table = document.getElementById("dataTable");
  const tbody = table.getElementsByTagName("tbody")[0];
  const rows = tbody.getElementsByTagName("tr");
  let sqlCommands = "";
  function updateSqlCommands() {
    sqlCommands = "";
    for (let i = 0; i < rows.length; i++) {
      const row = rows[i];
      const checkbox = row.querySelector("input[type='checkbox']");
      if (!checkbox.checked) {
        const columns = row.getElementsByTagName("td");
        if (columns.length >= 9) {
          const column1Value = columns[1].textContent.trim();
          const column2Value = columns[2].textContent.trim();
          const column3Value = columns[3].textContent.trim();
          const column4Value = columns[4].textContent.trim();
          const column5Value = columns[5].textContent.trim();
          const column5ValueWithoutCommas = column5Value.replace(/,/g, "");
          const column6Value = columns[6].textContent.trim();
          const column7Value = columns[7].textContent.trim();
          const column8Value = columns[8].textContent.trim();
          const sqlInsert = `INSERT INTO invoices (invoice_number, customer_id, item, total_amount, total_amount_after_percentage,total_amount_in_eur,total_amount_in_eur_after_percentage,created_date) VALUES ('${column1Value}', '${column2Value}', '${column3Value}', '${column4Value}', '${column5ValueWithoutCommas}', '${column6Value}', '${column7Value}', '${column8Value}');`;
          sqlCommands += sqlInsert + "\n";
        }
      }
    }
    const sqlCommandsElement = document.getElementById("sqlCommands");
    sqlCommandsElement.textContent = sqlCommands;
  }
  const checkboxes = table.querySelectorAll("input[type='checkbox']");
  checkboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", updateSqlCommands);
  });
  updateSqlCommands();
} catch (error) {
  const sqlCommandsElement = document.getElementById("sqlCommands");
  sqlCommandsElement.textContent =
    "An error occurred while processing the SQL commands: " + error.message;
}
document.getElementById("submitSql").addEventListener("click", function () {
  const sqlCommands = document.getElementById("sqlCommands").textContent;
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "send_sql_commands.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.send("sqlCommands=" + encodeURIComponent(sqlCommands));
  xhr.onreadystatechange = function () {
    if (this.readyState == 4) {
      if (this.status == 200) {
        Swal.fire({
          icon: "success",
          title: "Success",
          text: "Data sent successfully!",
          confirmButtonText: "OK",
        });
        $("#invoiceList").DataTable().ajax.reload();
      } else {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "An error occurred while sending data.",
          confirmButtonText: "OK",
        });
        console.error("Error Status:", xhr.status);
        console.error("Error Response:", xhr.responseText);
      }
    }
  };
});
