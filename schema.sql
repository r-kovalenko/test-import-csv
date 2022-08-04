create table merchants
(
    mid bigint(18) unsigned not null
        primary key,
    dba varchar(100)        null,
    constraint merchants_mid_uindex
        unique (mid)
);

create table batches
(
    batch_date    date                 null,
    batch_ref_num decimal(24) unsigned null,
    mid           bigint(18) unsigned  null,
    batch_id      varchar(100)         not null
        primary key,
    constraint batches_id_uindex
        unique (batch_id),
    constraint batches_merchants_mid_fk
        foreign key (mid) references merchants (mid)
);


create table transactions
(
    trans_date      date                         null,
    trans_type      varchar(20)                  null,
    trans_card_type set ('VI', 'MC', 'AX', 'DC') null,
    trans_card_num  varchar(20)                  null,
    trans_amount    float                        null,
    batch_id        varchar(100)                 not null,
    trans_id        bigint unsigned auto_increment
        primary key,
    constraint transactions_batches_batch_id_fk
        foreign key (batch_id) references batches (batch_id)
);

