document.addEventListener('DOMContentLoaded', () => {
            const faqItems = document.querySelectorAll('.faq-item');

            // 1. Initialization: Ensure all items are correctly collapsed on load
            faqItems.forEach(item => {
                const answer = item.querySelector('.faq-answer');
                const toggleIcon = item.querySelector('.faq-toggle-icon i');

                // This runs once to fix the initial state from HTML
                if (item.classList.contains('active')) {
                    answer.style.maxHeight = answer.scrollHeight + 'px';
                    toggleIcon.classList.remove('fa-chevron-down');
                    toggleIcon.classList.add('fa-chevron-up');
                } else {
                    answer.style.maxHeight = '0';
                    toggleIcon.classList.remove('fa-chevron-up');
                    toggleIcon.classList.add('fa-chevron-down');
                }
            });


            // 2. Click listener logic
            faqItems.forEach(clickedItem => {
                const question = clickedItem.querySelector('.faq-question');

                question.addEventListener('click', () => {
                    const isCurrentlyActive = clickedItem.classList.contains('active');

                    // If the clicked item is already active, just close it (toggle off)
                    if (isCurrentlyActive) {
                        clickedItem.classList.remove('active');
                        clickedItem.querySelector('.faq-answer').style.maxHeight = '0';
                        clickedItem.querySelector('.faq-toggle-icon i').classList.remove('fa-chevron-up');
                        clickedItem.querySelector('.faq-toggle-icon i').classList.add('fa-chevron-down');
                        return; // Stop here if we just closed it
                    }

                    // Loop through all items to close any currently open one (Accordion behavior)
                    faqItems.forEach(itemToClose => {
                        if (itemToClose.classList.contains('active')) {
                            itemToClose.classList.remove('active');
                            itemToClose.querySelector('.faq-answer').style.maxHeight = '0';
                            itemToClose.querySelector('.faq-toggle-icon i').classList.remove('fa-chevron-up');
                            itemToClose.querySelector('.faq-toggle-icon i').classList.add('fa-chevron-down');
                        }
                    });

                    // Open the clicked item
                    clickedItem.classList.add('active');
                    const answer = clickedItem.querySelector('.faq-answer');
                    answer.style.maxHeight = answer.scrollHeight + 'px';
                    
                    const toggleIcon = clickedItem.querySelector('.faq-toggle-icon i');
                    toggleIcon.classList.remove('fa-chevron-down');
                    toggleIcon.classList.add('fa-chevron-up');
                });
            });
        });