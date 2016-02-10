select advertiser_subject,sum(open_cnt)/sum(sent_cnt) from advertiser_subject,subject_log where advertiser_subject.subject_id=subject_log.subject_id and campaign_id in (select campaign_id from 3rdparty_campaign where third_party_id in (5,6)) and advertiser_subject.status='A' group by advertiser_subject order by 2 desc limit 3; 
