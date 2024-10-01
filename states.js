document.addEventListener('DOMContentLoaded', () => {
  try {
    // Tab Management
    const tabs = document.querySelectorAll('.nav-link[data-bs-toggle="pill"]');
    const tabContents = document.querySelectorAll(".tab-pane");
    const activeTab = localStorage.getItem("activeTab");

    if (activeTab) {
      tabs.forEach(tab => {
        const isActive = tab.id === activeTab;
        tab.classList.toggle("active", isActive);
        tab.setAttribute("aria-selected", isActive.toString());
      });

      tabContents.forEach(content => {
        const isActive = content.id === activeTab.replace("-tab", "");
        content.classList.toggle("show", isActive);
        content.classList.toggle("active", isActive);
      });
    }

    tabs.forEach(tab => {
      tab.addEventListener("click", () => localStorage.setItem("activeTab", tab.id));
    });

    // Modal Management
    const myModalEl = document.getElementById("newInvoice");
    if (!myModalEl) throw new Error('Modal element with ID "newInvoice" not found.');

    const myModal = new bootstrap.Modal(myModalEl);

    myModalEl.addEventListener("show.bs.modal", event => {
      try {
        const button = event.relatedTarget;
        const channelId = button?.dataset?.channelId;
        if (!channelId) throw new Error("Channel ID not provided.");

        const youtubeAnalyticsValue = "Replace with actual value"; // TODO: Update with real value

        const channelDisplay = document.getElementById("channel_display");
        channelDisplay ? channelDisplay.textContent = channelId : console.warn('Element #channel_display not found.');

        const customerSelect = document.getElementById("customer_id");
        if (customerSelect) {
          customerSelect.selectedIndex = 2; // Select the third option (0-based index)
          [...customerSelect.options].forEach(option => {
            if (option.dataset.youtube === channelId) option.selected = true;
          });
        } else {
          console.warn('Select element with ID "customer_id" not found.');
        }

        const totalAmount = document.getElementById("total_amount");
        totalAmount ? totalAmount.value = youtubeAnalyticsValue : console.warn('Element #total_amount not found.');
      } catch (modalError) {
        console.error("Error during modal setup:", modalError);
      }
    });

    const createInvoiceBtn = document.querySelector(".krijo-fature-btn");
    if (createInvoiceBtn) {
      createInvoiceBtn.addEventListener("click", () => myModal.show());
    } else {
      console.warn('Button with class ".krijo-fature-btn" not found.');
    }

  } catch (error) {
    console.error("Initialization error:", error);
  }
});
