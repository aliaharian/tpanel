import React from 'react';
import Authenticated from '@/Layouts/Authenticated';
import { Head } from '@inertiajs/inertia-react';
import AuthenticatedAgency from '@/Layouts/AuthenticatedAgency';

export default function Dashboard(props) {
    console.log(props)
    return (
        <AuthenticatedAgency
            auth={props.auth}
            errors={props.errors}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">آژانس {props.info.agency_name} با مدیریت {props.info.user.name} {props.info.user.last_name}</h2>}
        >
            <Head title="داشبورد" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 bg-white border-b border-gray-200">از منوی بالا گزینه ی مربوطه را انتخاب کنید</div>
                    </div>
                </div>
            </div>
        </AuthenticatedAgency>
    );
}
