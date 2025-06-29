<script>
    console.log('maramqwhd');

    const orderSummaryPopupModal = (data) => {

        const defaultProfileImage =
            'https://s3.amazonaws.com/cdn.deliveryz.com/deliveryz-net/images/488993ced9f6a3d0570630da5b55ad6a.png';


        const tableRows = data.clients.map(client => `
        <tr>
            <td>
               <div class="w-4 h-4 rounded-5 summaryImageWrapper">
                    <img src="${client.profile || defaultProfileImage}" class="l1dzm6xy">
                </div>
            </td>
            <td>
                <div class="text-slide-wrapper">
                    <p class="text-slide"> ${client.full_name}</p>
                </div>

            </td>
            <td>${client.pending_orders_count}</td>
            <td>${client.in_progress_orders_count}</td>
            <td>${client.failed_count}</td>
            <td>${client.cancelled_orders_count}</td>
            <td>${client.delivered_orders_count}</td>
            <td>${client.avg_waiting_time}</td>
            <td>${client.avg_delivery_time}</td>
        </tr>
    `).join(''); // Join the array into a single string

        // Return the complete modal content with the generated table rows
        return `
        <div class="modal-content mainPopup orderSummaryContainer">
            <div class="mainCloseBtn position-absolute cursor-pointer" data-bs-dismiss="modal">
                <a class="close-order-popup-map">
                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M1.5 12C1.5 6.22386 6.22386 1.5 12 1.5C17.7761 1.5 22.5 6.22386 22.5 12C22.5 17.7761 17.7761 22.5 12 22.5C6.22386 22.5 1.5 17.7761 1.5 12ZM12 2.5C6.77614 2.5 2.5 6.77614 2.5 12C2.5 17.2239 6.77614 21.5 12 21.5C17.2239 21.5 21.5 17.2239 21.5 12C21.5 6.77614 17.2239 2.5 12 2.5ZM12 12.7071L9.52351 15.1835L8.81641 14.4764L11.2928 12L8.81641 9.52351L9.52351 8.81641L12 11.2929L14.4764 8.81641L15.1835 9.52351L12.7071 12L15.1835 14.4764L14.4764 15.1835L12 12.7071Z" fill="black"></path>
                    </svg>
                </a>
            </div>

            <div class="d-flex align-items-center mb-3">
                <p class="fs-128px fw-bold gray-585858 border-right pe-2">
                    Total: ${data.total_orders}
                </p>
                <p class="fs-128px fw-bold gray-585858 border-right px-2">
                    Delivered: ${data.delivered_orders}
                </p>
                <p class="fs-128px fw-bold gray-585858 ps-2">
                    Success Rate: ${data.success_rate}%
                </p>
            </div>

            <div class="scrollable-table orderSummary">
                <table class="table" id="driver_assign_table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Clients</th>
                            <th>Pending</th>
                            <th>In progress</th>
                            <th>Failed</th>
                            <th>Cancelled</th>
                            <th>Delivered</th>
                            <th>Avg operator waiting</th>
                            <th>Avg delivery</th>
                        </tr>
                    </thead>
                    <tbody id="assign-driver-table" class="text-center fs-112px gray-585858 fw-bold">
                        ${tableRows}
                    </tbody>
                </table>
            </div>
        </div>
    `;
    };

    $(document).on("mouseenter", ".text-slide-wrapper", function() {
            console.log("hovered")
            const $textSlide = $(this).find(".text-slide");
            const textContent = $textSlide.text();
 
            const textLength = textContent.length;
            const $wrapper = $(this);

            const textWidth = $textSlide[0].scrollWidth;
            const wrapperWidth = $wrapper.outerWidth();

            const moveDistance = textWidth - wrapperWidth;
            const calcValue = `-${moveDistance}px`;

            const keyframes = `
                @keyframes textSlide {
                    0% {
                        transform: translateX(0); /* Start position */
                    }
                    50% {
                        transform: translateX(${calcValue}); /* Move to the last letter */
                    }
                    100% {
                        transform: translateX(0); /* Return to start */
                    }
                }`;
            // Check if the keyframes are already appended to avoid duplication
            if ($("style#textSlideKeyframe").length === 0) {
                $("head").append(`<style id="textSlideKeyframe">${keyframes}</style>`);
            } else {
                // If style already exists, update the keyframes
                $("style#textSlideKeyframe").html(keyframes);
            }



        });

</script>
