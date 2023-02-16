import Authenticated from "@/Layouts/Authenticated";
import AuthenticatedAgency from "@/Layouts/AuthenticatedAgency";

const WatcherContainer = ({ children, auth, errors, admin, agency }) => {
    return admin ? (
        <Authenticated
            auth={auth}
            errors={errors}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    نمایش واچر
                </h2>
            }
        >
            {children}
        </Authenticated>
    ) : agency ? (
        <AuthenticatedAgency
            auth={auth}
            errors={errors}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    نمایش واچر
                </h2>
            }
        >
            {children}
        </AuthenticatedAgency>
    ) : (
        <>{children}</>
    );
};

export default WatcherContainer;
