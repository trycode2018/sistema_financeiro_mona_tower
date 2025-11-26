<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Relatório de Estudantes</h1>
                <p class="text-gray-600">Estatísticas e informações dos estudantes</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Filtros</h2>
            <form method="GET" action="{{ route('reports.students') }}">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="class" class="block text-sm font-medium text-gray-700 mb-2">Turma</label>
                        <select name="class" id="class"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition">
                            <option value="">Todas as Turmas</option>
                            @foreach($classes as $class)
                                <option value="{{ $class }}" {{ request('class') == $class ? 'selected' : '' }}>
                                    {{ $class }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="academic_year" class="block text-sm font-medium text-gray-700 mb-2">Ano Lectivo</label>
                        <select name="academic_year" id="academic_year"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition">
                            @foreach($academicYears as $year)
                                <option value="{{ $year }}" {{ $academicYear == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-school-primary text-white px-4 py-3 rounded-lg hover:bg-school-dark transition">
                            Aplicar Filtros
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Estatísticas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Total de Estudantes</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalStudents }}</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Com Pagamentos Pendentes</p>
                    <p class="text-2xl font-semibold text-red-600">{{ $studentsWithPendingPayments }}</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Dívida Total</p>
                    <p class="text-2xl font-semibold text-red-600">Kz {{ number_format($totalDebt, 2, ',', ' ') }}</p>
                </div>
            </div>
        </div>

        <!-- Tabela de Estudantes -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Lista de Estudantes</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estudante
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Turma
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Faturas
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Pago
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Saldo Pendente
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($students as $student)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $student->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $student->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $student->class }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $student->invoices->count() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                                Kz {{ number_format($student->invoices->sum('amount_paid'), 2, ',', ' ') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                Kz {{ number_format($student->invoices->whereIn('status', ['pending', 'overdue'])->sum('balance'), 2, ',', ' ') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>