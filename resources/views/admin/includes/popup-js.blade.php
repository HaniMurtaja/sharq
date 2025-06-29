

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct"
    crossorigin="anonymous"></script>

<script>
  $(document).ready(function () {
  const toggleBtn = $("#toggleCollapseBtn");
  const firstCollapse = $("#collapseFirst");
  const secondCollapse = $("#collapseSecond");
  const collapseWrapper = $(".collapse-wrapper");

  $(document).on("click", "#toggleCollapseBtn", function (e) {
    e.preventDefault(); // Prevent default button behavior

    const toggleBtn = $("#toggleCollapseBtn"); // Ensure toggleBtn is defined inside the event
    const firstCollapse = $("#collapseFirst");
    const secondCollapse = $("#collapseSecond");

    console.log(toggleBtn);

    // Check if the button currently says "See More"
    const isCollapsed = toggleBtn.text().trim() === "See More";

    if (isCollapsed) {
      firstCollapse.collapse("show");
      secondCollapse.collapse("show");
      updateToggleText();
    } else {
      firstCollapse.collapse("hide");
      secondCollapse.collapse("hide");
      updateToggleText();
    }
  });

  // Function to update the toggle button text
  const updateToggleText = () => {
    const firstCollapse = $("#collapseFirst");
    const secondCollapse = $("#collapseSecond");
    const toggleBtn = $("#toggleCollapseBtn");

    // Check if either of the collapses is currently open
    const isFirstOpen = firstCollapse.hasClass("show");
    const isSecondOpen = secondCollapse.hasClass("show");

    if (isFirstOpen || isSecondOpen) {
      toggleBtn.text("See Less");
    } else {
      toggleBtn.text("See More");
    }
  };

  const adjustWrapperHeight = () => {
    const isFirstOpen = firstCollapse.hasClass("show");
    const isSecondOpen = secondCollapse.hasClass("show");

    collapseWrapper.each(function () {
      if (isFirstOpen || isSecondOpen) {
        $(this).css({ height: "380px", overflowY: "auto" }); // Make it scrollable
      } else {
        $(this).css({ height: "fit-content", overflowY: "hidden" }); // Reset scroll behavior
      }
    });
  };

  // Add event listeners for when the collapse events are triggered
//   $(firstCollapse).on("shown.bs.collapse hidden.bs.collapse", () => {
//     updateToggleText();
//     adjustWrapperHeight();
//   });

//   $(secondCollapse).on("shown.bs.collapse hidden.bs.collapse", () => {
//     updateToggleText();
//     adjustWrapperHeight();
//   });

  // Adjust wrapper height on load and window resize
  adjustWrapperHeight();
  window.addEventListener("resize", adjustWrapperHeight);
});

    // // Driver Name Dragging
    // const driverName = document.querySelector('.driverNamePhone .driverData .driverName');

    // let isDraggingDriver = false;
    // let startXDriver, scrollLeftDriver;

    // // Mouse down event to start dragging
    // driverName.addEventListener('mousedown', (e) => {
    //     isDraggingDriver = true;
    //     driverName.classList.add('active');
    //     startXDriver = e.pageX - driverName.offsetLeft;
    //     scrollLeftDriver = driverName.scrollLeft;
    // });

    // // Mouse move event to handle the dragging
    // driverName.addEventListener('mousemove', (e) => {
    //     console.log(44);

    //     if (!isDraggingDriver) return;
    //     e.preventDefault();
    //     const x = e.pageX - driverName.offsetLeft;
    //     const walk = x - startXDriver; // Calculate distance moved
    //     driverName.scrollLeft = scrollLeftDriver - walk;
    // });

    // // Mouse up and leave events to stop dragging
    // driverName.addEventListener('mouseup', () => {
    //     isDraggingDriver = false;
    //     driverName.classList.remove('active');
    // });

    // driverName.addEventListener('mouseleave', () => {
    //     isDraggingDriver = false;
    //     driverName.classList.remove('active');
    // });

    // // Stepper Wrapper Dragging
    // const stepperContainer = document.querySelector('.stepper-wrapper');

    // let isDraggingStepper = false;
    // let startXStepper, scrollLeftStepper;

    // stepperContainer.addEventListener("mousedown", (e) => {
    //     isDraggingStepper = true;
    //     startXStepper = e.pageX - stepperContainer.offsetLeft;
    //     scrollLeftStepper = stepperContainer.scrollLeft;
    // });

    // stepperContainer.addEventListener("mouseleave", () => {
    //     isDraggingStepper = false;
    // });

    // stepperContainer.addEventListener("mouseup", () => {
    //     isDraggingStepper = false;
    // });

    // stepperContainer.addEventListener("mousemove", (e) => {
    //     if (!isDraggingStepper) return;
    //     e.preventDefault();
    //     const x = e.pageX - stepperContainer.offsetLeft;
    //     const scroll = (x - startXStepper) * 2; // Adjust scroll speed
    //     stepperContainer.scrollLeft = scrollLeftStepper - scroll;
    // });


    // // Define the stepper items
    // const stepperItems = document.querySelectorAll('.stepper-item');
    // let currentStep = 0; // To track the current active step

    // // Function to update the steps
    // function updateStepper() {
    //     // First, reset all items
    //     stepperItems.forEach(item => item.classList.remove('completed', 'active'));

    //     // Mark the completed steps
    //     for (let i = 0; i <= currentStep; i++) {
    //         stepperItems[i].classList.add('completed');
    //     }

    //     // Set the current active step
    //     if (currentStep < stepperItems.length) {
    //         stepperItems[currentStep].classList.add('active');
    //     }
    // }

    // // Function to increment the progress
    // function incrementProgress() {
    //     console.log(999);

    //     if (currentStep < stepperItems.length - 1) {
    //         currentStep++;
    //         updateStepper();
    //     }
    // }

    // // Example: Automatically increment the progress every 2 seconds
    // setInterval(incrementProgress, 2000);


    // document.addEventListener("DOMContentLoaded", () => {
    //     const toggleBtn = document.getElementById("toggleCollapseBtn");
    //     const firstCollapse = document.getElementById("collapseFirst");
    //     const secondCollapse = document.getElementById("collapseSecond");
    //     const collapseWrapper = document.querySelectorAll(".collapse-wrapper");


    // });
</script>
