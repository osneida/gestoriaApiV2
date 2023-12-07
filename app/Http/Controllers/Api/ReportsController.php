<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Contract;
use App\Models\ContractType;
use App\Models\Payroll;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Holidays;


class ReportsController extends Controller
{

    /**
     * Devuelve numero de nominas por empresa y tipo de contracto
     */
    public function getPayrollsReport()
    {
        $date = date("Y-m-d", strtotime(request("month") . "-01"));
        $endDate = date("Y-m-t", strtotime(request("month") . "-01"));
        $company = Company::where("active", true)->select(["name", "cif"])->select(['name'])->withCount([
            'payrolls as total' => function (
                $query
            ) {
                $query->where('period', request("month"));
            },
            "payrolls as indef_total" => function (
                $query
            ) use ($date, $endDate) {
                $query->where('period', request("month"))
                    ->whereHas('worker', function ($query) use ($date, $endDate) {
                        $query->where("archive", false)->whereHas('latestContract', function ($query) use ($date, $endDate) {
                            $query->whereIn('contract_type', [1, 2]);
                            $query->whereDate('contract_start_date', '<=', $endDate);
                            $query->where(function ($query) use ($date, $endDate) {

                                $query->whereDate('contract_end_date', '>', $date)->orWhereNull('contract_end_date');
                            });
                        });
                    });
            }, "payrolls as temporal_total" => function (
                $query
            ) use ($date, $endDate) {
                $query->where('period', request("month"))
                    ->whereHas('worker', function ($query) use ($date, $endDate) {
                        $query->where("archive", false)->whereHas('latestContract', function ($query) use ($date, $endDate) {
                            $query->whereIn('contract_type', [3, 4, 5]);
                            $query->whereDate('contract_start_date', '<=', $endDate);
                            $query->where(function ($query) use ($date, $endDate) {

                                $query->whereDate('contract_end_date', '>', $date)->orWhereNull('contract_end_date');
                            });
                        });
                    });
            },
            "contracts as finiquito" => function (
                $query
            ) use ($date, $endDate) {
                $query->whereDate('contract_end_date', "<", $endDate);
                $query->whereHas("workerFiles", function ($query) {
                    $query->where("type", "finiquito");
                });
            }

        ])->with([
            "contracts" => function (
                $query
            ) use ($date, $endDate) {
                $query->whereDate('contract_end_date', "<", $endDate);
                //$query->with("workerFiles");
            }
        ]);

        $tipos = ContractType::get();
        foreach ($tipos as $tipo) {

            $company->withCount(["payrolls as $tipo->name" => function (
                $query
            ) use ($tipo, $date, $endDate) {
                $query->where('period', request("month"))
                    ->whereHas('worker', function ($query) use ($tipo, $date, $endDate) {
                        $query->where("archive", false)->whereHas('latestContract', function ($query) use ($tipo, $date, $endDate) {
                            $query->where('contract_type', $tipo->id);
                            $query->whereDate('contract_start_date', '<=', $endDate);
                            $query->where(function ($query) use ($date, $endDate) {

                                $query->whereDate('contract_end_date', '>', $date)->orWhereNull('contract_end_date');
                            });
                        });
                    });
            }]);
        }

        $itemsPerPage = (int)request('itemsPerPage');
        $sortBy = request('sortBy') ? request('sortBy')[0] : 'name';
        $order = request('sortDesc') && request('sortDesc')[0] == 'true' ? 'desc' : 'asc';

        switch ($sortBy) {
            case 'name':
            case 'total': {
                    $company->orderBy($sortBy, $order);
                }
        }

        if ($itemsPerPage == 'all')  return response()->json($company->get());
        return response()->json($company->paginate($itemsPerPage != 'undefined' ? $itemsPerPage : 10));
    }

    /**
     * Devuelve el numero de trabajadores activos por empresa
     */
    public function getActiveWorkers()
    {
        $date = date("Y-m-d", strtotime(request("month") . "-01"));
        $endDate = date("Y-m-t", strtotime(request("month") . "-01"));
        //return $endDate;

        if (JWTAuth::parseToken()->authenticate()->role->id === User::COMPANY_ROLE) {
            return [
                "temporal" =>
                Worker::whereHas('contracts', function ($query) use ($date, $endDate) {
                    $query->whereDate('contract_start_date', '<=', $endDate);
                    $query->where(function ($query) use ($date) {
                        $query->whereDate('contract_end_date', '>', $date)->orWhereNull('contract_end_date');
                    });
                    $query->whereIn('contract_type', [3, 4, 5]);
                })->where("archive", false)->whereHas('companies', function ($query) {
                    $query->where('id', request("company_id") ?? JWTAuth::parseToken()->authenticate()->company->id);
                })->count(),
                "indefinit" => Worker::whereHas('contracts', function ($query) use ($date, $endDate) {
                    $query->whereDate('contract_start_date', '<=', $endDate);

                    $query->where(function ($query) use ($date) {
                        $query->whereDate('contract_end_date', '>', $date)->orWhereNull('contract_end_date');
                    });

                    $query->whereIn('contract_type', [1, 2]);
                })->where("archive", false)->whereHas('companies', function ($query) {
                    $query->where('id', request("company_id") ?? JWTAuth::parseToken()->authenticate()->company->id);
                })->count(),
                "total" => Worker::whereHas('contracts', function ($query) use ($date, $endDate) {
                    $query->whereDate('contract_start_date', '<=', $endDate);
                    $query->where(function ($query) use ($date) {
                        $query->whereDate('contract_end_date', '>', $date)->orWhereNull('contract_end_date');
                    });
                })->where("archive", false)->whereHas('companies', function ($query) {
                    $query->where('id', request("company_id") ?? JWTAuth::parseToken()->authenticate()->company->id);
                })->count(),
                "months" => [$date, $endDate]
            ];
        }

        //return response()->json($date);
        $company = Company::where("active", true)->select(['name', 'cif'])->withCount(['workers as total' => function ($query) use ($date) {
            $query->where("archive", false)->whereHas('contracts', function ($query) use ($date) {
                //$query->whereDate('contract_start_date', '<=', $date);
                $query->whereDate('contract_end_date', '>', $date)->orWhereNull('contract_end_date');
            });
        }]);
        $itemsPerPage = (int)request('itemsPerPage');
        $sortBy = request('sortBy') ? request('sortBy')[0] : 'name';
        $order = request('sortDesc') && request('sortDesc')[0] == 'true' ? 'desc' : 'asc';
        switch ($sortBy) {
            case 'name':
            case 'total': {
                    $company->orderBy($sortBy, $order);
                }
        }

        if ($itemsPerPage == "all") return $company->get();
        return response()->json($company->paginate($itemsPerPage != 'undefined' ? $itemsPerPage : 10));
    }

    public function getActiveWorkersMonthly()
    {
        $result = [];
        $month = explode("-", request("month"));
        $to = now()->year == $month[0] ? now()->month : 12;
        $from = now()->year == $month[0] ? now()->month + 1 : 13;
        for ($i = 1; $i <= $to; $i++) {
            $date = date("Y-m-d", strtotime($month[0] . "-" . $i . "-01"));
            $endDate = date("Y-m-t", strtotime($month[0] . "-" . $i . "-01"));


            $result[] = [
                "temporal" =>
                Worker::whereHas('contracts', function ($query) use ($date, $endDate) {
                    $query->whereDate('contract_start_date', '<=', $endDate);
                    $query->where(function ($query) use ($date) {
                        $query->whereDate('contract_end_date', '>', $date)->orWhereNull('contract_end_date');
                    });
                    $query->whereIn('contract_type', [3, 4, 5]);
                })->where("archive", false)->whereHas('companies', function ($query) {
                    $query->where('id', request("company_id") ?? JWTAuth::parseToken()->authenticate()->company->id);
                })->count(),
                "indefinit" => Worker::whereHas('contracts', function ($query) use ($date, $endDate) {
                    $query->whereDate('contract_start_date', '<=', $endDate);

                    $query->where(function ($query) use ($date) {
                        $query->whereDate('contract_end_date', '>', $date)->orWhereNull('contract_end_date');
                    });

                    $query->whereIn('contract_type', [1, 2]);
                })->whereHas('companies', function ($query) {
                    $query->where('id', request("company_id") ?? JWTAuth::parseToken()->authenticate()->company->id);
                })->count(),
                "total" => Worker::whereHas('contracts', function ($query) use ($date, $endDate) {
                    $query->whereDate('contract_start_date', '<=', $endDate);
                    $query->where(function ($query) use ($date) {
                        $query->whereDate('contract_end_date', '>', $date)->orWhereNull('contract_end_date');
                    });
                })->where("archive", false)->whereHas('companies', function ($query) {
                    $query->where('id', request("company_id") ?? JWTAuth::parseToken()->authenticate()->company->id);
                })->count()
            ];
        }
        for ($j = $from; $j <= 12; $j++) {
            $result[] = ["total" => null, "indefinite" => null, "temporary" => null, "month" => $j];
        }

        return $result;
    }

    public function getBillingReport()
    {
        $month = explode("-", request("month"));

        $company = Company::where("active", true)->select(["name", "cif"])->withCount([
            'contracts as altes' => function ($query) use ($month) {
                $query->whereYear('contract_start_date', '=', $month[0]);
                $query->whereMonth('contract_start_date', '=', $month[1]);
                $query->whereNull('modificated_contract_id');
                //TODO contract_start_date +2 !== contract_end_date
                $query->whereRaw("TIMESTAMPDIFF(DAY, contracts.contract_start_date, contracts.contract_end_date) > 2");
            },
            "contracts as modificacions" => function ($query) use ($month) {
                $query->whereMonth('created_at', '=', $month[1])->whereYear('created_at', '=', $month[0])->whereNotNull('modificated_contract_id');
            },
            "contracts as baixes" => function ($query) use ($month) {
                //TODO contract_start_date +2 !== contract_end_date

                $query->whereRaw("TIMESTAMPDIFF(DAY, contracts.contract_start_date, contracts.contract_end_date) > 2");


                $query->whereMonth('updated_at', '=', $month[1])->whereYear('updated_at', '=', $month[0])->whereNotNull('not_enjoyed_vacancies');
            },
            "contracts as altes_baixes" => function ($query) use ($month) {
                $query->whereYear('contract_start_date', '=', $month[0]);
                $query->whereMonth('contract_start_date', '=', $month[1]);
                //TODO contract_start_date +2 === contract_end_date
                $query->whereRaw("TIMESTAMPDIFF(DAY, contracts.contract_start_date, contracts.contract_end_date) <= 2");
            },
            'payrolls as payrolls' => function (
                $query
            ) {
                $query->where('period', request("month"));
            },
        ]);
        $workersPerPage = (int) request('itemsPerPage');
        if ($workersPerPage == "all") return $company->get();
        return $company->paginate($workersPerPage != 'undefined' ? $workersPerPage : 10);
    }

    /**
     * Devuelve los contratos dados de alta por empresa i tipo de contrato
     */
    public function registerReportNew()
    {
        $month = explode("-", request("month"));
        if (JWTAuth::parseToken()->authenticate()->role->id === User::COMPANY_ROLE) {
            if (request("year"))

                return [
                    "temporal" =>
                    Worker::whereHas('contracts', function ($query) use ($month) {
                        $query->whereYear('contract_start_date', '=', $month[0]);
                        $query->whereIn('contract_type', [3, 4, 5])->whereNull('modificated_contract_id');
                    })->where("archive", false)->whereHas('companies', function ($query) {
                        $query->where('id', request("company_id") ?? JWTAuth::parseToken()->authenticate()->company->id);
                    })->count(), "indefinit" => Worker::whereHas('contracts', function ($query) use ($month) {
                        $query->whereYear('contract_start_date', '=', $month[0]);
                        $query->whereIn('contract_type', [1, 2])->whereNull('modificated_contract_id');
                    })->whereHas('companies', function ($query) {
                        $query->where('id', request("company_id") ?? JWTAuth::parseToken()->authenticate()->company->id);
                    })->count()
                ];

            return [
                "temporal" =>
                Worker::whereHas('contracts', function ($query) use ($month) {
                    $query->whereYear('contract_start_date', '=', $month[0]);
                    $query->whereMonth('contract_start_date', '=', $month[1]);
                    $query->whereIn('contract_type', [3, 4, 5])->whereNull('modificated_contract_id');
                })->where("archive", false)->whereHas('companies', function ($query) {
                    $query->where('id', request("company_id") ?? JWTAuth::parseToken()->authenticate()->company->id);
                })->count(), "indefinit" => Worker::whereHas('contracts', function ($query) use ($month) {
                    $query->whereYear('contract_start_date', '=', $month[0]);
                    $query->whereMonth('contract_start_date', '=', $month[1]);
                    $query->whereIn('contract_type', [1, 2])->whereNull('modificated_contract_id');
                })->whereHas('companies', function ($query) {
                    $query->where('id', request("company_id") ?? JWTAuth::parseToken()->authenticate()->company->id);
                })->count()
            ];
        } else {

            $company = Company::where("active", true)->select(["name", "cif"])->withCount([
                'workers as total' => function ($query) use ($month) {
                    $query->where("archive", false)->whereHas('contracts', function ($query) use ($month) {
                        $query->whereYear('contract_start_date', '=', $month[0]);
                        $query->whereMonth('contract_start_date', '=', $month[1]);
                        $query->whereNull('modificated_contract_id');
                    });
                },
                "workers as indef_total" => function ($query) use ($month) {
                    $query->where("archive", false)->whereHas('contracts', function ($query) use ($month) {
                        $query->whereYear('contract_start_date', '=', $month[0]);
                        $query->whereMonth('contract_start_date', '=', $month[1]);
                        $query->whereIn('contract_type', [1, 2]);

                        $query->whereNull('modificated_contract_id');
                    });
                },
                "workers as temporal_total" => function ($query) use ($month) {
                    $query->where("archive", false)->whereHas('contracts', function ($query) use ($month) {
                        $query->whereYear('contract_start_date', '=', $month[0]);
                        $query->whereMonth('contract_start_date', '=', $month[1]);
                        $query->whereIn('contract_type', [3, 4, 5]);
                        $query->whereNull('modificated_contract_id');
                    });
                }


            ]);
            $types = ContractType::get();

            foreach ($types as $type) {
                $company->where("archive", false)->withCount(["workers as $type->name" => function ($query) use ($type, $month) {
                    $query->whereHas('contracts', function ($query) use ($type, $month) {
                        $query->whereYear('contract_start_date', '=', $month[0]);
                        $query->whereMonth('contract_start_date', '=', $month[1]);
                        $query->where('contract_type', $type->id)->whereNull('modificated_contract_id');
                    });
                }]);
            }



            $sortBy = request('sortBy') ? request('sortBy')[0] : "name";
            $order = request('sortDesc') && request('sortDesc')[0] == 'true' ? 'desc' : 'asc';

            switch ($sortBy) {
                case 'name':
                case 'total':
                case 'indefinite':
                case 'indefinite_discontinuous':
                case 'temporary':
                case 'obra_servei':
                case 'becari': {
                        $company->orderBy($sortBy, $order);
                    }
            }

            $workersPerPage = (int) request('itemsPerPage');
            if ($workersPerPage == "all") return $company->get();
            return $company->paginate($workersPerPage != 'undefined' ? $workersPerPage : 10);
        }
    }


    /**
     * Devuelve el numeor de modificacio
     */
    public function registerReportUpdated()
    {
        $month = explode("-", request("month"));

        if (JWTAuth::parseToken()->authenticate()->role->id === User::COMPANY_ROLE) {
            if (request("year"))
                return [
                    "hores" => Worker::whereHas('contracts', function ($query) use ($month) {
                        $query->whereYear('created_at', '=', $month[0]);
                        $query->whereNotNull('modificated_contract_id');
                        $query->where("end_motive", "=", 1);
                    })->where("archive", false)->whereHas('companies', function ($query) {
                        $query->where('id', request("company_id") ?? JWTAuth::parseToken()->authenticate()->company->id);
                    })->count(),
                    "renovacions" => Worker::whereHas('contracts', function ($query) use ($month) {
                        $query->whereYear('created_at', '=', $month[0]);
                        $query->whereNotNull('modificated_contract_id');
                        $query->where("end_motive", "=", 2);
                    })->where("archive", false)->whereHas('companies', function ($query) {
                        $query->where('id', request("company_id") ?? JWTAuth::parseToken()->authenticate()->company->id);
                    })->count(),
                    "transformacions" => Worker::whereHas('contracts', function ($query) use ($month) {
                        $query->whereYear('created_at', '=', $month[0]);
                        $query->whereNotNull('modificated_contract_id');
                        $query->where("end_motive", "=", 3);
                    })->where("archive", false)->whereHas('companies', function ($query) {
                        $query->where('id', request("company_id") ?? JWTAuth::parseToken()->authenticate()->company->id);
                    })->count()
                ];

            return [
                "hores" => Worker::whereHas('contracts', function ($query) use ($month) {
                    $query->whereYear('created_at', '=', $month[0]);
                    $query->whereMonth('created_at', '=', $month[1]);
                    $query->whereNotNull('modificated_contract_id');
                    $query->where("end_motive", "=", 1);
                })->where("archive", false)->whereHas('companies', function ($query) {
                    $query->where('id', request("company_id") ?? JWTAuth::parseToken()->authenticate()->company->id);
                })->count(),
                "renovacions" => Worker::whereHas('contracts', function ($query) use ($month) {
                    $query->whereYear('created_at', '=', $month[0]);
                    $query->whereMonth('created_at', '=', $month[1]);
                    $query->whereNotNull('modificated_contract_id');
                    $query->where("end_motive", "=", 2);
                })->where("archive", false)->whereHas('companies', function ($query) {
                    $query->where('id', request("company_id") ?? JWTAuth::parseToken()->authenticate()->company->id);
                })->count(),
                "transformacions" => Worker::whereHas('contracts', function ($query) use ($month) {
                    $query->whereYear('created_at', '=', $month[0]);
                    $query->whereMonth('created_at', '=', $month[1]);
                    $query->whereNotNull('modificated_contract_id');
                    $query->where("end_motive", "=", 3);
                })->where("archive", false)->whereHas('companies', function ($query) {
                    $query->where('id', request("company_id") ?? JWTAuth::parseToken()->authenticate()->company->id);
                })->count()
            ];
        }

        $companies = Company::where("active", true)->select(["name", "cif"])->withCount([
            "contracts as total_modified" => function ($query) use ($month) {
                $query->whereMonth('created_at', '=', $month[1])->whereYear('created_at', '=', $month[0])->whereNotNull('modificated_contract_id');
            },
            "contracts as renovations" => function ($query) use ($month) {
                $query->whereMonth('created_at', '=', $month[1])->whereYear('created_at', '=', $month[0])->whereNotNull('modificated_contract_id')->where("end_motive", "=", 2);
            },
            "contracts as hores" => function ($query) use ($month) {
                $query->whereMonth('created_at', '=', $month[1])->whereYear('created_at', '=', $month[0])->whereNotNull('modificated_contract_id')->where("end_motive", "=", 1);
            },
            "contracts as tranformations" => function ($query) use ($month) {
                $query->whereMonth('created_at', '=', $month[1])->whereYear('created_at', '=', $month[0])->whereNotNull('modificated_contract_id')->where("end_motive", "=", 3);
            },

        ]);

        $sortBy = request('sortBy') ? request('sortBy')[0] : "name";
        $order = request('sortDesc') && request('sortDesc')[0] == 'true' ? 'desc' : 'asc';

        switch ($sortBy) {
            case 'name':
            case 'total_modified': {
                    $companies->orderBy($sortBy, $order);
                }
        }

        $workersPerPage = (int) request('itemsPerPage');
        if ($workersPerPage == "all") return $companies->get();
        return $companies->paginate($workersPerPage != 'undefined' ? $workersPerPage : 10);
    }
    public function registerReportTerminated()
    {
        $month = explode("-", request("month"));



        if (JWTAuth::parseToken()->authenticate()->role->id === User::COMPANY_ROLE) {
            if (request("year"))

                return [
                    "voluntaria" => Worker::whereHas('contracts', function ($query) use ($month) {
                        $query->whereYear('updated_at', '=', $month[0]);
                        //$query->whereNotNull('not_enjoyed_vacancies');
                        $query->where("end_motive", "=", 1);
                    })->whereHas('companies', function ($query) {
                        $query->where('id', request("company_id") ?? JWTAuth::parseToken()->authenticate()->company->id);
                    })->where("archive", false)->count(),
                    "ficontracte" => Worker::whereHas('contracts', function ($query) use ($month) {
                        $query->whereYear('updated_at', '=', $month[0]);
                        //$query->whereNotNull('not_enjoyed_vacancies');
                        $query->where("end_motive", "=", 2);
                    })->whereHas('companies', function ($query) {
                        $query->where('id', request("company_id") ?? JWTAuth::parseToken()->authenticate()->company->id);
                    })->where("archive", false)->count(),
                    "acomiadament" => Worker::whereHas('contracts', function ($query) use ($month) {
                        $query->whereYear('updated_at', '=', $month[0]);
                        //$query->whereNotNull('not_enjoyed_vacancies');
                        $query->where("end_motive", "=", 3);
                    })->whereHas('companies', function ($query) {
                        $query->where('id', request("company_id") ?? JWTAuth::parseToken()->authenticate()->company->id);
                    })->where("archive", false)->count()
                ];
            return [
                "voluntaria" => Worker::whereHas('contracts', function ($query) use ($month) {
                    $query->whereYear('updated_at', '=', $month[0]);
                    $query->whereMonth('updated_at', '=', $month[1]);
                    //$query->whereNotNull('not_enjoyed_vacancies');
                    $query->where("end_motive", "=", 1);
                })->whereHas('companies', function ($query) {
                    $query->where('id', request("company_id") ?? JWTAuth::parseToken()->authenticate()->company->id);
                })->where("archive", false)->count(),
                "ficontracte" => Worker::whereHas('contracts', function ($query) use ($month) {
                    $query->whereYear('updated_at', '=', $month[0]);
                    $query->whereMonth('updated_at', '=', $month[1]);
                    //$query->whereNotNull('not_enjoyed_vacancies');
                    $query->where("end_motive", "=", 2);
                })->whereHas('companies', function ($query) {
                    $query->where('id', request("company_id") ?? JWTAuth::parseToken()->authenticate()->company->id);
                })->where("archive", false)->count(),
                "acomiadament" => Worker::whereHas('contracts', function ($query) use ($month) {
                    $query->whereYear('updated_at', '=', $month[0]);
                    $query->whereMonth('updated_at', '=', $month[1]);
                    //$query->whereNotNull('not_enjoyed_vacancies');
                    $query->where("end_motive", "=", 3);
                })->whereHas('companies', function ($query) {
                    $query->where('id', request("company_id") ?? JWTAuth::parseToken()->authenticate()->company->id);
                })->where("archive", false)->count()
            ];
        }


        $companies = Company::where("active", true)->select(["name", "cif"])->withCount([
            "contracts as total" => function ($query) use ($month) {
                $query->whereMonth('contract_end_date', '=', $month[1])->whereYear('contract_end_date', '=', $month[0])/*->whereNotNull('not_enjoyed_vacancies')*/;
            }

        ]);

        $sortBy = request('sortBy') ? request('sortBy')[0] : "name";
        $order = request('sortDesc') && request('sortDesc')[0] == 'true' ? 'desc' : 'asc';

        switch ($sortBy) {
            case 'name':
            case 'total_modified': {
                    $companies->orderBy($sortBy, $order);
                }
        }

        $workersPerPage = (int) request('itemsPerPage');
        if ($workersPerPage == "all") return $companies->get();
        return $companies->paginate($workersPerPage != 'undefined' ? $workersPerPage : 10);
    }

    public function resume()
    {
        $month = explode("-", request("month"));
        $date = strtotime(request("month") . "-01");

        $data = [];

        $data["companies"] = Company::where("active", true)->select(["name", "cif"])->where("active", true)->count();
        $data["inscriptions"] = Contract::whereMonth("contract_start_date", $month[1])->whereYear("contract_start_date", $month[0])->whereNull('modificated_contract_id')->count();

        $data["modifications"] =
            Contract::whereMonth("created_at", $month[1])->whereYear("created_at", $month[0])->whereNotNull('modificated_contract_id')->count();
        $data["baixes"] =
            Contract::whereMonth("contract_end_date", $month[1])->whereYear("contract_end_date", $month[0])/*->whereNotNull('not_enjoyed_vacancies')*/->count();
        $data["payrolls"] = Payroll::where("period", request("month"))->count();

        $data["active_workers"] =
            Worker::whereHas('contracts', function ($query) use ($date) {
                //$query->whereDate('contract_start_date', '<=', $date);
                $query->whereDate('contract_end_date', '>', $date)->orWhereNull('contract_end_date');
            })->where("archive", false)->count();

        return response()->json($data);
    }

    public function payrollsResume()
    {
        $month = explode("-", request("month"));

        $data = [];
        $data["indef"] = Payroll::where("period", request("month"))->whereHas('worker', function ($query) use ($month) {
            $query->whereHas('latestContract', function ($query) use ($month) {
                $query->whereIn('contract_type', [1, 2]);
                $query->whereMonth("contract_start_date", "<=", $month[1]);
                $query->whereYear("contract_start_date", "<=", $month[0]);
                $query->where(function ($query) use ($month) {
                    $query->where(function ($query) use ($month) {

                        $query->whereMonth("contract_end_date", ">=", $month[1]);
                        $query->whereYear("contract_end_date", ">=", $month[0]);
                    });
                    $query->orWhereNull("contract_end_date");
                });
            });
        })->count();
        $data["temporary"] = Payroll::where("period", request("month"))->whereHas('worker', function ($query) use ($month) {
            $query->whereHas('latestContract', function ($query) use ($month) {
                $query->whereIn('contract_type', [3, 4, 5]);
                $query->whereMonth("contract_start_date", "<=", $month[1]);
                $query->whereYear("contract_start_date", "<=", $month[0]);
                $query->where(function ($query) use ($month) {
                    $query->where(function ($query) use ($month) {

                        $query->whereMonth("contract_end_date", ">=", $month[1]);
                        $query->whereYear("contract_end_date", ">=", $month[0]);
                    });
                    $query->orWhereNull("contract_end_date");
                });
            });
        })->count();

        return $data;
    }
    public function payrollsResumeAnual()
    {
        $month = explode("-", request("month"));

        $data = [];
        $data["indef"] = Payroll::where("period", 'LIKE', "$month[0]-%")->whereHas('worker', function ($query) use ($month) {
            $query->whereHas('latestContract', function ($query) use ($month) {
                $query->whereIn('contract_type', [1, 2]);

                $query->whereYear("contract_start_date", "<=", $month[0]);
                $query->where(function ($query) use ($month) {
                    $query->where(function ($query) use ($month) {


                        $query->whereYear("contract_end_date", ">=", $month[0]);
                    });
                    $query->orWhereNull("contract_end_date");
                });
            });
        })->count();
        $data["temporary"] = Payroll::where("period", 'LIKE', "$month[0]-%")->whereHas('worker', function ($query) use ($month) {
            $query->whereHas('latestContract', function ($query) use ($month) {
                $query->whereIn('contract_type', [3, 4, 5]);

                $query->whereYear("contract_start_date", "<=", $month[0]);
                $query->where(function ($query) use ($month) {
                    $query->where(function ($query) use ($month) {


                        $query->whereYear("contract_end_date", ">=", $month[0]);
                    });
                    $query->orWhereNull("contract_end_date");
                });
            });
        })->where("archive", false)->count();

        return $data;
    }


    public function movementsResume()
    {
        $month = explode("-", request("month"));
        $data = [];

        $data["indef"] =
            Contract::whereMonth("contract_start_date", $month[1])->whereYear("contract_start_date", $month[0])->whereNull('modificated_contract_id')->whereIn('contract_type', [1, 2])->count();

        $data["temporary"] =
            Contract::whereMonth("contract_start_date", $month[1])->whereYear("contract_start_date", $month[0])->whereNull('modificated_contract_id')->whereIn('contract_type', [3, 4, 5])->count();
        $data["mods"] =
            Contract::whereMonth("created_at", $month[1])->whereYear("created_at", $month[0])->whereNotNull('modificated_contract_id')->count();

        $data["baixes"] =
            Contract::whereMonth("updated_at", $month[1])->whereYear("created_at", $month[0])->whereNotNull('not_enjoyed_vacancies')->count();

        return $data;
    }

    public function movementsResumeAnual()
    {
        $month = explode("-", request("month"));
        $data = [];

        $data["indef"] =
            Contract::whereYear("contract_start_date", $month[0])->whereNull('modificated_contract_id')->whereIn('contract_type', [1, 2])->count();

        $data["temporary"] =
            Contract::whereYear("contract_start_date", $month[0])->whereNull('modificated_contract_id')->whereIn('contract_type', [3, 4, 5])->count();
        $data["mods"] =
            Contract::whereYear("created_at", $month[0])->whereNotNull('modificated_contract_id')->count();

        $data["baixes"] =
            Contract::whereYear("updated_at", $month[0])->whereNotNull('not_enjoyed_vacancies')->count();

        return $data;
    }

    public function getHolidays()
    {
        Log::info('mensaje de traer vacaciones',['data']);
        $year = now()->year;
        Log::info('mensaje de traer year 1',['data' => $year]);
        if (request("month"))
            $year = explode("-", request("month"))[0];
            Log::info('mensaje de traer year 2',['data' => $year]);
        $workers = Worker::whereHas("latestContract", function ($q) use ($year) {
            $q->where("company_id", request("company_id") ?? JWTAuth::parseToken()->authenticate()->company->id);
            $q->whereDate("contract_start_date", "<=", "$year-12-31");
            $q->where(function ($q) use ($year) {
                $q->whereDate("contract_end_date", ">=", "$year-01-01");
                $q->orWhereNull("contract_end_date");
            });
        })->where("archive", false)->where(function ($workers) {
            $workers->whereHas('companies', function ($q) {
                $q->whereHas("users", function ($q) {
                    $q->where("id", auth()->user()->id);
                    $q->where('users_companies.role', 'gestor');
                });
            });
            Log::info('mensaje de traer workers 1',['data' => $workers]);
            $workers->orWhere(function ($q) {
                $q->whereHas('contracts', function ($q) {
                    $q->where('creator_id', auth()->user()->id);
                });
                $q->whereHas('companies', function ($q) {
                    $q->whereHas("users", function ($q) {
                        $q->where("id", auth()->user()->id);
                        $q->where('users_companies.role', 'sub_gestor');
                    });
                });
            });
            Log::info('mensaje de traer workers 2',['data' => $workers]);
        });;

        //$workersPerPage = (int) request('itemsPerPage');
        $workers = ["data" => $workers->get(), "total" => $workers->count()];
        Log::info('mensaje de traer workers 3',['data' => $workers]);

        foreach ($workers["data"] as $w) {
            $w->holidays_has = HolidaysController::calculateHolidays($w->id, request("company_id") ?? JWTAuth::parseToken()->authenticate()->company->id, $year);

            $w->holidays_spend = HolidaysController::spendedHolidays($w->id, request("company_id") ?? JWTAuth::parseToken()->authenticate()->company->id, $year);


            $w->holidays_remain = $w->holidays_has - $w->holidays_spend;
        }
        Log::info('mensaje de traer workers 4',['data' => $workers]);
        return $workers;
    }
}
