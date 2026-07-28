<?php

return [

    'required' => ':attribute အကွက်ကို ဖြည့်ရန်လိုအပ်ပါသည်။',
    'numeric' => ':attribute အကွက်သည် ဂဏန်းဖြစ်ရမည်။',
    'min' => [
        'numeric' => ':attribute အကွက်သည် အနည်းဆုံး :min ဖြစ်ရမည်။',
        'string' => ':attribute အကွက်သည် အနည်းဆုံး :min လုံးရရမည်။',
    ],
    'max' => [
        'string' => ':attribute အကွက်သည် :max လုံးထက် မပိုရပါ။',
    ],
    'email' => ':attribute အကွက်သည် မှန်ကန်သော အီးမေးလ် ဖြစ်ရမည်။',
    'date' => ':attribute အကွက်သည် မှန်ကန်သော ရက်စွဲ ဖြစ်ရမည်။',
    'confirmed' => ':attribute အတည်ပြုချက် မကိုက်ညီပါ။',

    'attributes' => [
        'name' => 'အမည်',
        'email' => 'အီးမေးလ်',
        'password' => 'စကားဝှက်',
        'amount' => 'ပမာဏ',
        'spent_on' => 'အသုံးစရိတ်ရက်စွဲ',
    ],

];
