<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>id</th>
                                            <th>Nama Bank</th>
                                            <th>Data Kas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dataRekening as $row)
                                        <tr>
                                            <td>{{$row->id}}</td>
                                            <td>{{$row->nama_rekening}}</td>
                                            <td>
                                                @foreach ($row->kas as $kas)
                                                {{$kas['tanggal']}} <br>
                                                @endforeach
                                            </td>
                                        </tr>
                                        @endforeach 
                                    </tbody>
                                </table>