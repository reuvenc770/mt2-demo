select email_addr from tranzact_unsub where unsub_date >= date_sub(curdate(),interval 1 day) and unsub_date < curdate();
