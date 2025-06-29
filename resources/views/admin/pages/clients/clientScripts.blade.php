<script>
    $(document).ready(function() {
        var status;
        let currentClientRequest = null;
        let driverCurrentRequest = null;










        function fetchClientData() {
            var clientCardHtml = '',
                if (currentClientRequest) {
                    currentClientRequest.abort();
                }

            currentClientRequest = $.ajax({
                url: '{{ route('client-list') }}',
                method: 'GET',
                data: {
                    page: PAGE_NUMBER,

                    search: $('#client_search').val()
                },
                success: function(response) {
                    const userRole = response.user_role;

                    response.clients.forEach(function(item) {


                        var customerCardHtml = ` 
                <div class="itemlistContainer" data-id="${item.id}" data-type="client">
                                <div class="itemlistCard">
                                    <div class="itemListInfoContainer">
                                    <div class="itemListIcon">
                                    <img src="${item.shop_profile || 'https://via.placeholder.com/150'}"alt="client image" width="100" height="100">
                                    </div>

                                    <div class="itemListInfo">
                                        <div class="text-slide-wrapper">
                                            <p class="text-slide"> "BK" ${item.full_name}</p>
                                        </div>

                                        <small>${item.id}</small>
                                    </div>
                                    </div>
                                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M14.9603 5.39966C14.6674 5.10677 14.1926 5.10677 13.8997 5.39966C13.6068 5.69256 13.6068 6.16743 13.8997 6.46032L18.6893 11.25H3.5C3.08579 11.25 2.75 11.5858 2.75 12C2.75 12.4142 3.08579 12.75 3.5 12.75H18.6893L13.8997 17.5397C13.6068 17.8326 13.6068 18.3074 13.8997 18.6003C14.1926 18.8932 14.6674 18.8932 14.9603 18.6003L21.0303 12.5303C21.171 12.3897 21.25 12.1989 21.25 12C21.25 11.8011 21.171 11.6103 21.0303 11.4697L14.9603 5.39966Z" fill="#1A1A1A"></path></svg>
                                </div>
                                <div class="itemListDivider"></div>
                                <div class="itemListBadges">
                                    <div class="itemListBadge">
                                        <p>${item.city}</p>
                                    </div>
                                    <div class="itemListBadge">
                                        <p><span>${item.balance}</span> SAR</p>
                                    </div>
                                    <div class="itemListBadge">
                                        <p><span>${item.branch_count}</span> Branch</p>
                                    </div>
                                    <div class="itemListBadge">
                                        <p><span>${item.order_count}</span> Orders</p>
                                    </div>
                                </div>
                            </div> 
                `;

                         clientCardHtml .= customerCardHtml ,
                        ;
                    });

                   





                    PAGE_NUMBER++;
                },
                complete: function() {
                    currentClientRequest = null;
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
            console.log(clientCardHtml);
            
            return clientCardHtml;
        }



        $('.order_items').on('scroll', function() {
            var $this = $(this);
            if ($this.scrollTop() + $this.innerHeight() >= $this[0].scrollHeight) {

                fetchData(status, orders_details, $(".collapseContent-" + status).find('.order_items'));
                // PAGE_NUMBER++;
            }
        });
    });
</script>
