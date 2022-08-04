-- Query 1: display all transactions for a batch (filter: merchant_id + batch_date + batch_ref_num)
select *
    from transactions
    where batch_id = "102041127099952824_12024578317868064506566_2018-05-05";

-- Query 2: display statistics for a batch (filter: merchant_id + batch_date + batch_ref_num)
--          grouped by transaction card type
select transactions.trans_card_type, COUNT(*) as count
    from transactions
    where batch_id = "102041127099952824_12024578317868064506566_2018-05-05"
    group by trans_card_type;


-- Query 3: display top 10 merchants (by total amount) for a given date range (batch_date)
--          merchant id, merchant name, total amount, number of transactions
select m.mid, m.dba, sum(t.trans_amount) as trans_amount_sum, count(t.trans_id)
    from merchants m
    join batches b on m.mid = b.mid
    join transactions t on b.batch_id = t.batch_id
    where b.batch_date = "2018-05-05"
    group by m.mid order by trans_amount_sum desc
    limit 10;
