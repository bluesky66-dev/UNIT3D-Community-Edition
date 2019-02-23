<?php

return [
    'accepted'   => 'দ্য :attribute গ্রহণ করা আবশ্যক।',
    'active_url' => 'দ্য :attribute একটি বৈধ URL নয়',
    'after'      => 'দ্য :attribute পরে একটি তারিখ হতে হবে :date।',
    'alpha'      => 'দ্য :attribute শুধুমাত্র অক্ষর থাকতে পারে',
    'alpha_dash' => 'দ্য :attribute শুধুমাত্র অক্ষর, সংখ্যা এবং ড্যাশ থাকতে পারে।',
    'alpha_num'  => 'দ্য :attribute শুধুমাত্র অক্ষর এবং সংখ্যা থাকতে পারে',
    'array'      => 'দ্য :attribute একটি অ্যারে হতে হবে।',
    'attributes' => [
    ],
    'before'  => 'দ্য :attribute আগে একটি তারিখ হতে হবে :date।',
    'between' => [
        'array'   => 'দ্য :attribute মধ্যে থাকতে হবে :min এবং :max আইটেম।',
        'file'    => 'দ্য :attribute মধ্যে থাকা আবশ্যক :min এবং :max কিলোবাইট।',
        'numeric' => 'দ্য :attribute মধ্যে থাকা আবশ্যক :min এবং :max।',
        'string'  => 'দ্য :attribute মধ্যে থাকা আবশ্যক :min এবং :max অক্ষর।',
    ],
    'boolean'   => 'দ্য :attribute ক্ষেত্র সত্য বা মিথ্যা হতে হবে।',
    'confirmed' => 'দ্য :attribute নিশ্চিতকরণ মেলে না।',
    'custom'    => [
        'attribute-name' => [
            'rule-name' => 'কাস্টম বার্তা',
        ],
    ],
    'date'           => 'দ্য :attribute একটি বৈধ তারিখ নয়।',
    'date_format'    => 'দ্য :attribute ফর্ম্যাটের সাথে মেলে না :format।',
    'different'      => 'দ্য :attribute এবং :other ভিন্ন হতে হবে।',
    'digits'         => 'দ্য :attribute অবশ্যই :digits ডিজিটের।',
    'digits_between' => 'দ্য :attribute মধ্যে থাকা আবশ্যক :min এবং :max ডিজিটের।',
    'dimensions'     => 'দ্য :attribute অবৈধ ইমেজ মাত্রা রয়েছে',
    'distinct'       => 'দ্য :attribute ক্ষেত্রের একটি ডুপ্লিকেট মান রয়েছে।',
    'email'          => 'দ্য :attribute একটি বৈধ ইমেইল ঠিকানা আবশ্যক.',
    'exists'         => 'নির্বাচিত :attribute অবৈধ.',
    'file'           => 'দ্য :attribute একটি ফাইল হতে হবে',
    'filled'         => 'দ্য :attribute আপনি উত্তর দিবেন না.',
    'image'          => 'দ্য :attribute একটি ছবি হতে হবে',
    'in'             => 'নির্বাচিত :attribute অবৈধ.',
    'in_array'       => 'দ্য :attribute ক্ষেত্রের মধ্যে বিদ্যমান নেই :other।',
    'integer'        => 'দ্য :attribute একটি পূর্ণসংখ্যা হতে হবে',
    'ip'             => 'দ্য :attribute একটি বৈধ IP ঠিকানা হতে হবে।',
    'json'           => 'দ্য :attribute একটি বৈধ JSON স্ট্রিং হতে হবে।',
    'max'            => [
        'array'   => 'দ্য :attribute আরো বেশী হতে পারে না :max আইটেম।',
        'file'    => 'দ্য :attribute তুলনায় বড় হতে পারে না :max কিলোবাইট।',
        'numeric' => 'দ্য :attribute তুলনায় বড় হতে পারে না :max।',
        'string'  => 'দ্য :attribute তুলনায় বড় হতে পারে না :max অক্ষর।',
    ],
    'mimes'     => 'দ্য :attribute অবশ্যই একটি ফাইল হতে হবে: :values।',
    'mimetypes' => 'দ্য :attribute অবশ্যই একটি ফাইল হতে হবে: :values।',
    'min'       => [
        'array'   => 'দ্য :attribute অন্তত অবশ্যই থাকতে হবে :min আইটেম।',
        'file'    => 'দ্য :attribute নূন্যতম হতে হবে :min কিলোবাইট।',
        'numeric' => 'দ্য :attribute নূন্যতম হতে হবে :min।',
        'string'  => 'দ্য :attribute নূন্যতম হতে হবে :min অক্ষর।',
    ],
    'not_in'               => 'নির্বাচিত :attribute অবৈধ.',
    'numeric'              => 'দ্য :attribute অবশ্যই একটি সংখ্যা হবে.',
    'present'              => 'দ্য :attribute ক্ষেত্র উপস্থিত হতে হবে।',
    'regex'                => 'দ্য :attribute বিন্যাসটি অবৈধ।',
    'required'             => 'দ্য :attribute আপনি উত্তর দিবেন না.',
    'required_if'          => 'দ্য :attribute ক্ষেত্র প্রয়োজন হয় যখন :other হয় :value।',
    'required_unless'      => 'দ্য :attribute যদি ক্ষেত্রের প্রয়োজন হয় না :other ভিতরে আছে :values।',
    'required_with'        => 'দ্য :attribute ক্ষেত্র প্রয়োজন হয় যখন :values উপস্থিত.',
    'required_with_all'    => 'দ্য :attribute ক্ষেত্র প্রয়োজন হয় যখন :values উপস্থিত.',
    'required_without'     => 'দ্য :attribute ক্ষেত্র প্রয়োজন হয় যখন :values উপস্থিত নেই।',
    'required_without_all' => 'দ্য :attribute ক্ষেত্র যখন প্রয়োজন কেউ না :values উপস্থিত আছেন.',
    'same'                 => 'দ্য :attribute এবং :other মেলানো.',
    'size'                 => [
        'array'   => 'দ্য :attribute অবশ্যই থাকতে হবে :size আইটেম।',
        'file'    => 'দ্য :attribute অবশ্যই :size কিলোবাইট।',
        'numeric' => 'দ্য :attribute অবশ্যই :size।',
        'string'  => 'দ্য :attribute অবশ্যই :size অক্ষর।',
    ],
    'string'   => 'দ্য :attribute একটি স্ট্রিং হতে হবে।',
    'timezone' => 'দ্য :attribute একটি বৈধ জোন হতে হবে।',
    'unique'   => 'দ্য :attribute আগেই নেয়া হয়েছে.',
    'uploaded' => 'দ্য :attribute আপলোড করতে ব্যর্থ হয়েছে',
    'url'      => 'দ্য :attribute বিন্যাসটি অবৈধ।',
];
