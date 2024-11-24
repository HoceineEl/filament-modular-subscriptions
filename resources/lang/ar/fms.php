<?php

return [
    'resources' => [
        'plan' => [
            'name' => 'الخطة',
            'singular_name' => 'الخطة',
            'fields' => [
                'name' => 'الاسم',
                'slug' => 'الاسم المختصر',
                'description' => 'الوصف',
                'is_active' => 'نشط',
                'price' => 'السعر',
                'modules' => 'الوحدات',
                'currency' => 'العملة',
                'trial_period' => 'فترة التجربة',
                'trial_interval' => 'وحدة فترة التجربة',
                'invoice_period' => 'فترة الفاتورة',
                'invoice_interval' => 'وحدة فترة الفاتورة',
                'grace_period' => 'فترة السماح',
                'grace_interval' => 'وحدة فترة السماح',
                'module' => 'الوحدة',
                'module_limit' => 'حد الاستخدام',
                'module_price' => 'سعر الوحدة',
                'module_settings' => 'إعدادات الوحدة',
                'setting_key' => 'مفتاح الإعداد',
                'setting_value' => 'قيمة الإعداد',
                'modules_count' => 'عدد الوحدات',
                'is_pay_as_you_go' => 'الدفع حسب الاستخدام',
            ],
            'hints' => [
                'module_limit' => 'اتركه فارغًا للاستخدام غير المحدود',
            ],
            'placeholders' => [
                'setting_key' => 'أدخل مفتاح الإعداد',
                'setting_value' => 'أدخل قيمة الإعداد',
            ],
            'actions' => [
                'add_module' => 'إضافة وحدة',
                'collapse_all_modules' => 'طي جميع الوحدات',
            ],
            'tabs' => [
                'details' => 'التفاصيل',
                'billing' => 'الفوترة',
                'usage' => 'الاستخدام',
                'modules' => 'الوحدات',
                'pricing' => 'التسعير',
            ],
        ],
        'subscription' => [
            'name' => 'الاشتراك',
            'singular_name' => 'الاشتراك',
            'fields' => [
                'plan_id' => 'الخطة',
                'subscribable_type' => 'نوع المشترك',
                'subscribable_id' => 'المشترك',
                'starts_at' => 'تاريخ البدء',
                'ends_at' => 'تاريخ الانتهاء',
                'trial_ends_at' => 'تاريخ انتهاء التجربة',
                'status' => 'الحالة',
            ],
        ],
        'module' => [
            'name' => 'الوحدة',
            'singular_name' => 'الوحدة',
            'fields' => [
                'name' => 'الاسم',
                'class' => 'الفئة',
                'is_active' => 'نشط',
                'is_persistent' => 'استخدام مستمر',
                'is_persistent_help' => 'إذا تم تمكينه ، سيتم الاحتفاظ ببيانات الاستخدام عند تجديد الاشتراك',
            ],
        ],
        'module_usage' => [
            'name' => 'استخدام الوحدة',
            'singular_name' => 'استخدام الوحدة',
            'fields' => [
                'subscription_id' => 'الاشتراك',
                'module_id' => 'الوحدة',
                'usage' => 'الاستخدام',
                'pricing' => 'التسعير',
                'calculated_at' => 'تاريخ الحساب',
                'subscriber' => 'المشترك',
            ],
            'actions' => [
                'calculate' => 'حساب',
                'calculate_usage' => 'حساب الاستخدام',
                'calculate_usage_success' => 'تم حساب الاستخدام بنجاح',
            ],
        ],
        'invoice' => [
            'name' => 'الفواتير',
            'singular_name' => 'فاتورة',
            'fields' => [
                'invoice_number' => 'رقم الفاتورة',
                'subscription_id' => 'رقم الاشتراك',
                'amount' => 'المبلغ',
                'status' => 'الحالة',
                'due_date' => 'تاريخ الاستحقاق',
                'paid_at' => 'تاريخ الدفع',
            ],
            'no_items' => 'لا يوجد عناصر لهذه الفاتورة',
            'actions' => [
                'pay' => 'دفع المستحقات',
            ],
            'invoice_title' => 'فاتورة :subscriber - :id - :date',
            'payment_pending' => 'تم إرسال الفاتورة بنجاح. في انتظار التأكيد من الجهة المختصة',
        ],
        'payment' => [
            'name' => 'المدفوعات',
            'singular_name' => 'الدفع',
            'fields' => [
                'invoice_id' => 'رقم الفاتورة',
                'amount' => 'المبلغ',
                'payment_method' => 'طريقة الدفع',
                'transaction_id' => 'رقم المعاملة',
                'status' => 'الحالة',
                'created_at' => 'تاريخ الإنشاء',
                'reviewed_at' => 'تاريخ المراجعة',
                'admin_notes' => 'ملاحظات المسؤول',
                'notes' => 'ملاحظات',
                'reviewed_by' => 'مراجعة بواسطة',
                'subscribable_name' => 'اسم المشترك',
                'subscriber' => 'المشترك',
                'receipt_file' => 'إيصال الدفع',
            ],
            'actions' => [
                'approve' => 'تأكيد',
                'reject' => 'رفض',
            ],
            'sections' => [
                'payment_details' => 'تفاصيل الدفع',
            ],
            'receipt_help_text' => 'يرجى رفع ملف لإيصال الدفع الخاص بك لتأكيد الدفع.',

        ],
    ],
    'menu_group' => [
        'subscription_management' => 'إدارة الاشتراكات',
    ],
    'interval' => [
        'day' => 'يوم',
        'week' => 'أسبوع',
        'month' => 'شهر',
        'year' => 'سنة',
    ],
    'status' => [
        'active' => 'نشط',
        'cancelled' => '��لغي',
        'expired' => 'منتهي',
        'pending' => 'قيد الانتظار',
        'paid' => 'مدفوع',
        'unpaid' => 'غير مدفوع',
        'rejected' => 'مرفوض',
        'partially_paid' => 'مدفوع جزئيا',
    ],
    'tenant_subscription' => [
        'your_subscription' => 'اشتراكك',
        'current_subscription' => 'الاشتراك الحالي',
        'plan' => 'الخطة',
        'status' => 'الحالة',
        'started_on' => 'تاريخ البدء',
        'unlimited' => 'غير محدود',
        'ends_on' => 'تاريخ الانتهاء',
        'subscription_details' => 'تفاصيل الاشتراك',
        'days_left' => 'الأيام المتبقية',
        'on_trial' => 'في فترة التجربة',
        'yes' => 'نعم',
        'no' => 'لا',
        'trial_ends_at' => 'تاريخ انتهاء التجربة',
        'no_active_subscription' => 'لا يوجد اشتراك نشط',
        'no_subscription_message' => 'ليس لديك اشتراك نشط حاليًا. يرجى اختيار خطة للاشتراك.',
        'available_plans' => 'الخطط المتاحة',
        'per' => 'لكل',
        'switch_plan_button' => 'تغيير الخطة',
        'select_plan' => 'اختر الخطة',
        'switch_to_plan' => 'تغيير إلى هذه الخطة',
        'current_plan' => 'الخطة الحالية',
        'plan_switched_successfully' => 'تم تغيير الخطة بنجاح',
        'plan_switch_failed' => 'فشل في تغيير الخطة',
        'statuses' => [
            'active' => 'نشط',
            'canceled' => 'ملغي',
            'expired' => 'منتهي',
        ],
        'subscription_navigation_label' => 'إدارة إشتراكك وخطتك الحالية',
        'current_subscription' => 'الاشتراك الحالي',
        'plan' => 'الخطة',
        'status' => 'الحالة',
        'statuses' => [
            'active' => 'نشط',
            'canceled' => 'ملغى',
            'expired' => 'منتهي الصلاحية',
            'past_due' => 'متأخر الدفع',
            'pending' => 'قيد الانتظار',
            'trialing' => 'في فترة التجربة',
            'rejected' => 'مرفوض',
        ],
        'started_on' => 'تاريخ البدء',
        'ends_on' => 'تاريخ الانتهاء',
        'subscription_details' => 'تفاصيل الاشتراك',
        'days_left' => 'الأيام المتبقية',
        'on_trial' => 'في فترة التجربة',
        'yes' => 'نعم',
        'no' => 'لا',
        'trial_ends_at' => 'تنتهي فترة التجربة في',
        'no_active_subscription' => 'لا يوجد اشتراك نشط',
        'no_subscription_message' => 'ليس لديك اشتراك نشط حالياً. يرجى اختيار خطة للاشتراك.',
        'available_plans' => 'الخطط المتاحة',
        'unlimited' => 'غير محدود',
        'current_plan' => 'الخطة الحالية',
        'switch_to_plan' => 'التبديل إلى هذه الخطة',
        'pay_as_you_go' => 'الدفع حسب الاستخدام',
        'subscription' => 'اشتراك',
        'per_unit' => 'لكل وحدة',
        'only_pay_for_what_you_use' => 'ادفع فقط مقابل ما تستخدمه',
        'included_features' => 'الميزات المضمنة',
        'usage_information' => 'معلومات الاستخدام',
        'billed_monthly' => 'يتم الفوترة شهرياً بناءً على الاستخدام الفعلي',
        'no_minimum_commitment' => 'لا يوجد حد أدنى للالتزام',
        'usage_tracked_realtime' => 'يتم تتبع الاستخدام في الوقت الفعلي',
        'start_using_pay_as_you_go' => 'ابدأ باستخدام الدفع حسب الاستخدام',
    ],
    'intervals' => [
        'day' => 'يوم',
        'week' => 'أسبوع',
        'month' => 'شهر',
        'year' => 'سنة',
    ],
    'widgets' => [
        'current_plan' => 'الخطة الحالية',
        'no_active_subscription' => 'لا يوجد اشتراك نشط',
        'subscribed_on' => 'تم الاشتراك في :date',
        'subscribe_to_plan' => 'اشترك في خطة',
        'days_left' => 'الأيام المتبقية',
        'expires_on' => 'ينتهي في :date',
        'available_plans' => 'الخطط المتاحة',
        'per' => 'لكل :interval',
    ],
    'invoice' => [
        'number' => 'رقم الفاتورة',
        'payment_pending' => 'تم إرسال الفاتورة بنجاح. في انتظار التأكيد من الجهة المختصة',
        'amount' => 'المبلغ',
        'status' => 'الحالة',
        'due_date' => 'تاريخ الاستحقاق',
        'view' => 'عرض',
        'details_title' => 'تفاصيل الفاتورة رقم :number',
        'invoice_number' => 'فاتورة رقم :number',
        'billing_to' => 'فاتورة إلى',
        'invoice_details' => 'تفاصيل الفاتورة',
        'date' => 'التاريخ',
        'description' => 'الوصف',
        'quantity' => 'الكمية',
        'unit_price' => 'سعر الوحدة',
        'total' => 'الإجمالي',
        'subscription_fee' => 'رسوم الاشتراك لخطة :plan',
        'module_usage' => 'استخدام وحدة :module',
        'download_pdf' => 'تحميل PDF',
        'items' => 'العناصر',
        'thank_you_message' => 'شكرًا لك على عملك معنا!',
        'invoice_title' => 'فاتورة :subscriber - رقم :id - بتاريخ :date',
        'view_payments' => 'عرض المدفوعات',
    ],
    'payment' => [
        'select_method' => 'اختر طريقة الدفع',
        'select_method_description' => 'اختر طريقة الدفع المفضلة لإتمام المعاملة.',
        'method' => 'طريقة الدفع',
        'success' => 'تم الدفع بنجاح!',
        'error' => 'فشل الدفع. يرجى المحاولة مرة أخرى.',
        'cancelled' => 'تم إلغاء الدفع.',
        'amount' => 'المبلغ',
        'rejected' => 'تم رفض الدفع',
        'admin_notes' => 'ملاحظات المسؤول',
        'submitted_by' => 'بواسطة',
        'receipt_file' => 'إيصال الدفع',
        'submitted_at' => 'في',
        'approved' => 'تم التأكيد  الدفع بنجاح',
        'subscription_renewed' => 'تم تجديد الاشتراك بنجاح',
        'partially_paid' => 'ت�� الدفع جزئيا',
    ],
    'file_entry' => [
        'view_file' => 'عرض الملف',
        'no_file' => 'لا يوجد ملف',
    ],
    'payment_methods' => [
        'bank_transfer' => 'تحويل بنكي',
        'cash' => 'نقدي',
        'paypal' => 'باي بال',
        'stripe' => 'سترايب',
    ],
    'invoice_status' => [
        'paid' => 'مدفوع',
        'partially_paid' => 'مدفوع جزئياً',
        'unpaid' => 'غير مدفوع',
        'overdue' => 'متأخر',
        'cancelled' => 'ملغي',
        'refunded' => 'مسترد',
    ],
    'messages' => [
        'upgrade_required' => 'عليك ترقية الخطة لاستخدام هذه الوحدة',
        'upgrade_to_continue_using' => 'يجب ترقية خطتك إلى :plan للتمكن من استخدام وحدة :module',
        'upgrade_now' => 'ترقية الخطة',
        'you_ve_reached_your_limit_for_this_module' => 'لقد وصلت إلى حد الاستخدام لهذه الوحدة',
        'you_have_to_renew_your_subscription_to_use_this_module' => 'عليك تجديد اشتراكك لاستخدام هذه الوحدة',
        'view_invoice' => 'عرض الفاتورة',
        'module_limit_warning' => 'تحذير: أنت تقترب من حد استخدام الوحدة',
        'subscription_ending_soon' => 'اشتراكك سينتهي قريباً',
    ],
];
